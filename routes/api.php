<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\ApiController;
use App\Http\Controllers\Frontend\ProxyController;
use App\Http\Controllers\Frontend\SchemaController;
use App\Http\Controllers\Frontend\AcupointController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

// ============================
//  Frontend API Routes
// ============================

// 站点信息
Route::get('/site-info', [ApiController::class, 'siteInfo']);

// 导航
Route::get('/navs', [ApiController::class, 'navs']);

// Banner
Route::get('/banners', [ApiController::class, 'banners']);

// 文章
Route::get('/articles', [ApiController::class, 'articles']);
Route::get('/articles/{slug}', [ApiController::class, 'articleDetail'])->where('slug', '.*');
Route::get('/articles/{slug}/comments', [ApiController::class, 'articleComments'])->where('slug', '.*');

// 栏目
Route::get('/columns', [ApiController::class, 'columns']);
Route::get('/columns/{slug}', [ApiController::class, 'columnDetail'])->where('slug', '.*');

// 分类
Route::get('/categories', [ApiController::class, 'categories']);

// 标签
Route::get('/tags', [ApiController::class, 'tags']);

// 友情链接
Route::get('/links', [ApiController::class, 'links']);

// 评论提交
Route::post('/comments', [ApiController::class, 'submitComment']);

// 远程资源代理（让前端拿到真实字节进度，绕过跨域）
Route::match(['get', 'head'], '/proxy', [ProxyController::class, 'fetch']);
Route::options('/proxy', [ProxyController::class, 'options']);



// Schema 类型树
Route::get('/schema/tree', [SchemaController::class, 'schemaTree']);

// Schema 类型平铺与属性（vite-schema 展示页，分开获取；属性按需加载单个类型）
Route::get('/schema/types', [SchemaController::class, 'schemaTypes']);
Route::get('/schema/summary', [SchemaController::class, 'schemaSummary']);
Route::get('/schema/properties/{id}', [SchemaController::class, 'schemaPropertiesForType'])->whereNumber('id');

// ============================
//  经络穴道数据 API（renti 项目用）
//  对应 renti/src/api/types.ts 的 SiteData 结构
// ============================
Route::get('/site-data', [AcupointController::class, 'siteData']);
Route::get('/acupoint-descs', [AcupointController::class, 'acupointDescs']);
