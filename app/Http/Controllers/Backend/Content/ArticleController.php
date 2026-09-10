<?php
namespace App\Http\Controllers\Backend\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Article\StoreArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
use App\Models\Article\Article;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * 文章列表
     */
    public function index(Request $request): JsonResponse
    {
        $model = $request->get('model');
        $query = Article::orderBy('sort', 'asc');

        // 搜索
        if (!empty($request->get('title'))) {
            $model = 'search';
        }

        switch (strtolower($model)) {
            case 'hasdel':
                $res = $query->onlyTrashed()->paginate(
                    $request->get('pageSize', $request->get('limit', 10))
                )->toArray();
                break;
            case 'search':
                $query = $query->where('title', 'like', '%' . $request->get('title') . '%');
                $res = $query->paginate(
                    $request->get('pageSize', $request->get('limit', 10))
                )->toArray();
                break;
            default:
                $res = $query->paginate(
                    $request->get('pageSize', $request->get('limit', 10))
                )->toArray();
                break;
        }

        return response()->json([
            'status' => true,
            'data' => $res['data'],
            'total' => $res['total'],
        ]);
    }

    /**
     * 创建文章
     */
    public function store(StoreArticleRequest $request): JsonResponse
    {
        $validated = $request->validated();
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }
        if (Article::create($validated)) {
            return response()->json(['status' => true, 'msg' => '成功添加文章']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }

    /**
     * 文章详情
     */
    public function show(): JsonResponse
    {

    }

    /**
     * 更新文章
     */
    public function update(UpdateArticleRequest $request, $id): JsonResponse
    {
        $validated = $request->validated();
        $article = Article::findOrFail($id);
        if ($article->update($validated)) {
            return response()->json(['status' => true, 'msg' => '成功更新文章']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }

    /**
     * 删除文章（软删除）
     */
    public function destroy(Request $request): JsonResponse
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return response()->json(['status' => false, 'msg' => '请选择删除项']);
        }
        if (Article::destroy($ids)) {
            return response()->json(['status' => true, 'msg' => '成功删除文章']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }

    /**
     * 恢复文章
     */
    public function restore(Request $request): JsonResponse
    {
        $id = $request->get('ids')[0];
        $article = Article::withTrashed()->find($id);
        if ($article->restore()) {
            return response()->json(['status' => true, 'msg' => '成功恢复文章']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }

    /**
     * 强制删除文章
     */
    public function force(Request $request): JsonResponse
    {
        $id = $request->get('ids')[0];
        $article = Article::withTrashed()->find($id);
        if ($article->forceDelete()) {
            return response()->json(['status' => true, 'msg' => '成功强制删除文章']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }

    /**
     * 文章数据（下拉选项等）
     */
    public function data(Request $request)
    {
        $title = $request->get('title');
        $query = new Article;
        if (!empty($title)) {
            $query = $query->where('title', 'like', '%' . $title . '%')->take(10)->get();
            foreach ($query as $value) {
                $ar['label'] = $value['title'];
                $ar['value'] = $value['id'];
                $res[] = $ar;
            }
        } else {
            $query = $query->get();
            foreach ($query as $value) {
                $ar['label'] = $value['title'];
                $ar['value'] = $value['id'];
                $res[] = $ar;
            }
        }
        if (count($query) <= 0) {
            return response()->json([['label' => '无数据', 'value' => '0']]);
        } else {
            return response()->json(['status' => true, 'data' => $res]);
        }
    }
}