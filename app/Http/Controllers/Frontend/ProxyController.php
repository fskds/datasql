<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ProxyController extends Controller
{
    /**
     * 远程资源代理：GET /api/proxy?url=...
     *
     * 用途：前端被浏览器 CORS 拦截的远程图片 / 字体 / 媒体，
     * 统一走本接口转发并带跨域头，从而让前端能 fetch 到真实字节进度。
     */
    public function fetch(Request $request): SymfonyResponse
    {
        $url = $request->query('url');
        if (!is_string($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['status' => false, 'msg' => '无效的 url 参数'], 422);
        }

        // 安全：仅允许 http/https
        if (!in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true)) {
            return response()->json(['status' => false, 'msg' => '仅允许 http/https 资源'], 422);
        }

        // 域名白名单（按需开启）：
        // 从设置中读取，避免硬编码；留空 = 不限制。
        $allowHosts = config('app.proxy_allow_hosts', []);
        if (!empty($allowHosts)) {
            if (!in_array(parse_url($url, PHP_URL_HOST), $allowHosts, true)) {
                return response()->json(['status' => false, 'msg' => '该域名不在代理白名单内'], 403);
            }
        }

        try {
            return $this->streamRemote($url, $request->header('Range'), $request->method());
        } catch (GuzzleException|Throwable $e) {
            return response()->json(['status' => false, 'msg' => '远程获取失败：' . $e->getMessage()], 502);
        }
    }

    /**
     * 逐块透传远程响应（支持 Range），保留 Content-Type / Content-Length / Content-Range
     */
    private function streamRemote(string $url, ?string $range, string $method = 'GET'): StreamedResponse
    {
        $client = new Client(['timeout' => 60, 'http_errors' => false]);

        $headers = $range ? ['Range' => $range] : [];
        $upstream = $client->request($method, $url, [
            'stream'     => true,
            'headers'    => $headers,
            'curl'       => [CURLOPT_FOLLOWLOCATION => true],
        ]);

        return new StreamedResponse(function () use ($upstream) {
            $body = $upstream->getBody();
            while (!$body->eof()) {
                echo $body->read(8192); // 8KB 一块转发
                flush();
            }
        }, $upstream->getStatusCode(), array_filter([
            'Content-Type'    => $upstream->getHeaderLine('Content-Type'),
            'Content-Length'  => $upstream->getHeaderLine('Content-Length'),
            'Content-Range'   => $upstream->getHeaderLine('Content-Range'),
            'Accept-Ranges'   => $upstream->getHeaderLine('Accept-Ranges'),
            'Access-Control-Allow-Origin'  => '*',
            'Access-Control-Expose-Headers' => 'Content-Length, Content-Range, Accept-Ranges',
            'Vary'            => 'Origin',
        ], fn ($v) => $v !== ''));
    }

    /**
     * CORS 预检
     */
    public function options(): SymfonyResponse
    {
        return response('', 204, [
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Methods'     => 'GET, OPTIONS',
            'Access-Control-Allow-Headers'     => 'Range, Origin, Content-Type, Accept',
            'Access-Control-Max-Age'           => '86400',
        ]);
    }
}