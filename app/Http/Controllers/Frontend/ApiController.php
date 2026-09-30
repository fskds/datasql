<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Setting\SiteInfo;
use App\Models\Menu\Nav;
use App\Models\Banner\Banner;
use App\Models\Article\Article;
use App\Models\Column\Column;
use App\Models\Column\Section;
use App\Models\Menu\Category;
use App\Models\Menu\Tag;
use App\Models\Link\Link;
use App\Models\Comment\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApiController extends Controller
{
    /**
     * 统一成功响应
     */
    private function success($data = null, string $msg = 'success', array $extra = []): JsonResponse
    {
        $response = [
            'status' => true,
            'msg'    => $msg,
            'data'   => $data,
        ];
        if (!empty($extra)) {
            $response = array_merge($response, $extra);
        }
        return response()->json($response);
    }

    /**
     * 统一失败响应
     */
    private function error(string $msg = 'error', $data = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => false,
            'msg'    => $msg,
            'data'   => $data,
        ], $code);
    }

    /**
     * 构建树形结构（通用方法）
     */
    private function buildTree($items, string $parentField = 'pId', string $childrenKey = 'children'): array
    {
        $tree = [];
        $map = [];

        foreach ($items as $item) {
            $itemData = $item instanceof \Illuminate\Database\Eloquent\Model ? $item->toArray() : (array) $item;
            $itemData[$childrenKey] = [];
            $map[$item->id] = $itemData;
        }

        foreach ($map as $id => &$itemData) {
            $parentId = $itemData[$parentField] ?? 0;
            if ($parentId && isset($map[$parentId])) {
                $map[$parentId][$childrenKey][] = &$itemData;
            } else {
                $tree[] = &$itemData;
            }
        }

        return $tree;
    }

    // ============================
    //  站点信息
    // ============================

    /**
     * 获取站点配置信息，按 groupid 分组
     * GET /api/site-info
     */
    public function siteInfo(): JsonResponse
    {
        $records = SiteInfo::where('status', 1)
            ->orderBy('groupid')
            ->orderBy('id')
            ->get();

        $grouped = $records->groupBy('groupid')->map(function ($group, $key) {
            return [
                'groupid' => $key,
                'items'   => $group->values()->toArray(),
            ];
        })->values();

        return $this->success($grouped);
    }

    // ============================
    //  导航
    // ============================

    /**
     * 获取导航列表（树形结构，pId=0 为根）
     * GET /api/navs
     */
    public function navs(): JsonResponse
    {
        $navs = Nav::where('status', 1)
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        $tree = $this->buildTree($navs, 'pId');

        return $this->success($tree);
    }

    // ============================
    //  Banner
    // ============================

    /**
     * 获取 Banner 列表，支持 ?column_id= 和 ?category_id= 筛选
     * GET /api/banners
     */
    public function banners(Request $request): JsonResponse
    {
        $query = Banner::where('status', 1)->orderBy('sort')->orderBy('id');

        if ($request->filled('column_id')) {
            $columnId = (int) $request->input('column_id');
            $query->whereHas('columns', function ($q) use ($columnId) {
                $q->where('content_banner_column.column_id', $columnId);
            });
        }

        if ($request->filled('category_id')) {
            $categoryId = (int) $request->input('category_id');
            $query->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('content_banner_category.category_id', $categoryId);
            });
        }

        $banners = $query->get();

        return $this->success($banners);
    }

    // ============================
    //  文章
    // ============================

    /**
     * 文章列表（分页），支持 ?category_id=, ?tag=, ?keyword=, ?flag=, ?per_page=
     * GET /api/articles
     */
    public function articles(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        $query = Article::where('status', 1)
            ->where(function ($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            })
            ->orderBy('sort', 'desc')
            ->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc');

        // 分类筛选
        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->input('category_id'));
        }

        // 标签筛选（通过 keywords 字段模糊匹配）
        if ($request->filled('tag')) {
            $tag = $request->input('tag');
            $query->where(function ($q) use ($tag) {
                $q->where('keywords', 'like', "%{$tag}%")
                  ->orWhere('title', 'like', "%{$tag}%");
            });
        }

        // 关键词搜索
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('content', 'like', "%{$keyword}%")
                  ->orWhere('keywords', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // 标记筛选：s=推荐, c=热门, o=置顶
        if ($request->filled('flag')) {
            $flags = explode(',', $request->input('flag'));
            foreach ($flags as $flag) {
                $flag = trim($flag);
                switch ($flag) {
                    case 's':
                        $query->where('flag_s', 1);
                        break;
                    case 'c':
                        $query->where('flag_c', 1);
                        break;
                    case 'o':
                        $query->where('flag_o', 1);
                        break;
                }
            }
        }

        $paginator = $query->paginate($perPage);

        return $this->success($paginator->items(), 'success', [
            'total'       => $paginator->total(),
            'per_page'    => $paginator->perPage(),
            'current_page'=> $paginator->currentPage(),
            'last_page'   => $paginator->lastPage(),
        ]);
    }

    /**
     * 文章详情（通过 slug），浏览量 +1
     * GET /api/articles/{slug}
     */
    public function articleDetail(string $slug): JsonResponse
    {
        $article = Article::where('slug', $slug)
            ->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            })
            ->first();

        if (!$article) {
            return $this->error('文章不存在');
        }

        // 浏览量 +1
        Article::where('id', $article->id)->increment('views');

        $article->refresh();

        return $this->success($article);
    }

    // ============================
    //  栏目
    // ============================

    /**
     * 栏目列表，每个栏目附带其 sections
     * GET /api/columns
     */
    public function columns(): JsonResponse
    {
        $columns = Column::where('status', 1)
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        $columns->load(['sections' => function ($query) {
            $query->where('status', 1)->orderBy('sort');
        }]);

        $columns->load(['banners' => function ($query) {
            $query->where('status', 1);
        }]);

        return $this->success($columns);
    }

    /**
     * 栏目详情（通过 slug），附带 sections 和文章列表
     * GET /api/columns/{slug}
     */
    public function columnDetail(string $slug): JsonResponse
    {
        $column = Column::where('slug', $slug)
            ->where('status', 1)
            ->first();

        if (!$column) {
            return $this->error('栏目不存在');
        }

        // 加载 sections
        $column->load(['sections' => function ($query) {
            $query->where('status', 1)->orderBy('sort');
        }]);

        // 加载 banners（Banner 关系按 pivot.sort 排序）
        $column->load(['banners' => function ($query) {
            $query->where('status', 1);
        }]);

        $data = $column->toArray();

        // 查询该栏目下的文章（通过栏目关联的导航 ID 关联分类，再查文章）
        // 若栏目有 nav_id，则查找该导航下所有分类对应的文章
        $articleQuery = Article::where('status', 1)
            ->where(function ($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            })
            ->orderBy('sort', 'desc')
            ->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc');

        if ($column->nav_id) {
            // 获取该导航下所有子导航 ID，再通过导航关联的分类查文章
            $navIds = Nav::where('id', $column->nav_id)
                ->orWhere('pId', $column->nav_id)
                ->pluck('id')
                ->toArray();

            if (!empty($navIds)) {
                // 查找这些导航关联的栏目，再通过栏目的 sections 或直接关联找文章
                // 这里采用一个简化方案：通过相同 nav_id 的栏目对应的分类来关联文章
                // 由于没有直接的 article-column 关系，使用 category 作为中间桥梁
            }
        }

        // 由于数据库中没有直接的 article-column 关系，这里返回空数组。
        // 如需关联，可在 articles 表中添加 column_id 字段后修改此方法。
        $data['articles'] = [];

        return $this->success($data);
    }

    // ============================
    //  分类
    // ============================

    /**
     * 分类列表（树形结构）
     * GET /api/categories
     */
    public function categories(): JsonResponse
    {
        $categories = Category::where('status', 1)
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        $tree = $this->buildTree($categories, 'pId');

        return $this->success($tree);
    }

    // ============================
    //  标签
    // ============================

    /**
     * 标签列表
     * GET /api/tags
     */
    public function tags(): JsonResponse
    {
        $tags = Tag::where('status', 1)
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        return $this->success($tags);
    }

    // ============================
    //  友情链接
    // ============================

    /**
     * 友情链接列表，按 sort 排序
     * GET /api/links
     */
    public function links(): JsonResponse
    {
        $links = Link::where('status', 1)
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        return $this->success($links);
    }

    // ============================
    //  评论
    // ============================

    /**
     * 文章评论列表（通过文章 slug 查询，树形结构，仅 status=1）
     * GET /api/articles/{slug}/comments
     */
    public function articleComments(string $slug): JsonResponse
    {
        $article = Article::where('slug', $slug)
            ->where('status', 1)
            ->first();

        if (!$article) {
            return $this->error('文章不存在');
        }

        $comments = Comment::where('article_id', $article->id)
            ->where('status', 1)
            ->orderBy('created_at')
            ->get();

        $tree = $this->buildTree($comments, 'parent_id');

        return $this->success($tree);
    }

    /**
     * 提交评论
     * POST /api/comments
     */
    public function submitComment(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'article_id'   => 'required|integer|exists:content_articles,id',
                'content'      => 'required|string|max:2000',
                'author_name'  => 'required|string|max:50',
                'author_email' => 'nullable|email|max:100',
                'parent_id'    => 'nullable|integer|exists:content_comments,id',
            ], [
                'article_id.required'   => '文章ID不能为空',
                'article_id.exists'     => '文章不存在',
                'content.required'      => '评论内容不能为空',
                'content.max'           => '评论内容不能超过2000字',
                'author_name.required'  => '评论者名称不能为空',
                'author_name.max'       => '评论者名称不能超过50字',
                'author_email.email'    => '邮箱格式不正确',
                'author_email.max'      => '邮箱不能超过100字',
                'parent_id.exists'      => '父评论不存在',
            ]);

            $comment = Comment::create([
                'article_id'   => $validated['article_id'],
                'content'      => $validated['content'],
                'author_name'  => $validated['author_name'],
                'author_email' => $validated['author_email'] ?? null,
                'parent_id'    => $validated['parent_id'] ?? 0,
                'status'       => 1,
            ]);

            return $this->success($comment, '评论提交成功');

        } catch (ValidationException $e) {
            return $this->error('验证失败', $e->errors());
        }
    }
}