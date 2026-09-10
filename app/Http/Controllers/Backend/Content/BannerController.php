<?php
namespace App\Http\Controllers\Backend\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Banner\StoreBannerRequest;
use App\Http\Requests\Banner\UpdateBannerRequest;
use App\Models\Banner\Banner;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BannerController extends Controller
{
    /**
     * Banner列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = Banner::query();

        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->get('title') . '%');
        }

        if (!empty($request->get('model')) && $request->get('model') === 'hasdel') {
            $query = $query->onlyTrashed();
        }

        $res = $query->orderBy('sort')->paginate(
            $request->get('pageSize', $request->get('limit', 10))
        )->toArray();

        $result = [
            'status' => true,
            'data' => $res['data'],
            'total' => $res['total'],
        ];

        return response()->json($result);
    }

    /**
     * 创建Banner
     */
    public function store(StoreBannerRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $banner = Banner::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Banner创建成功',
            'data' => $banner,
        ], 201);
    }

    /**
     * Banner详情
     */
    public function show(Banner $banner): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $banner->load(['categories', 'columns']),
        ]);
    }

    /**
     * 更新Banner
     */
    public function update(UpdateBannerRequest $request, Banner $banner): JsonResponse
    {
        $validated = $request->validated();

        $banner->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Banner更新成功',
            'data' => $banner,
        ]);
    }

    /**
     * 删除Banner
     */
    public function destroy(Banner $banner): JsonResponse
    {
        $banner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Banner删除成功',
        ]);
    }
}
