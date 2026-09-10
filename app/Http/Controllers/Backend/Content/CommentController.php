<?php
namespace App\Http\Controllers\Backend\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Models\Comment\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    /**
     * 评论列表（按文章ID筛选）
     */
    public function index(Request $request): JsonResponse
    {
        $articleId = $request->get('article_id');
        $model = $request->get('model');
        $query = Comment::with(['article:id,title'])->orderBy('id', 'desc');

        if (!empty($articleId)) {
            $query = $query->where('article_id', $articleId);
        }

        switch (strtolower($model)) {
            case 'hasdel':
                $res = $query->onlyTrashed()->paginate(
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
     * 创建评论
     */
    public function store(StoreCommentRequest $request): JsonResponse
    {
        $validated = $request->validated();
        if (Comment::create($validated)) {
            return response()->json(['status' => true, 'msg' => '成功添加评论']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }

    /**
     * 评论详情
     */
    public function show(): JsonResponse
    {

    }

    /**
     * 更新评论
     */
    public function update(UpdateCommentRequest $request, $id): JsonResponse
    {
        $validated = $request->validated();
        $comment = Comment::findOrFail($id);
        if ($comment->update($validated)) {
            return response()->json(['status' => true, 'msg' => '成功更新评论']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }

    /**
     * 删除评论（软删除）
     */
    public function destroy(Request $request): JsonResponse
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return response()->json(['status' => false, 'msg' => '请选择删除项']);
        }
        if (Comment::destroy($ids)) {
            return response()->json(['status' => true, 'msg' => '成功删除评论']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }

    /**
     * 恢复评论
     */
    public function restore(Request $request): JsonResponse
    {
        $id = $request->get('ids')[0];
        $comment = Comment::withTrashed()->find($id);
        if ($comment->restore()) {
            return response()->json(['status' => true, 'msg' => '成功恢复评论']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }

    /**
     * 强制删除评论
     */
    public function force(Request $request): JsonResponse
    {
        $id = $request->get('ids')[0];
        $comment = Comment::withTrashed()->find($id);
        if ($comment->forceDelete()) {
            return response()->json(['status' => true, 'msg' => '成功强制删除评论']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }
}