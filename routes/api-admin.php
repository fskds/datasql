<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Backend\Api\AuthController;
use App\Http\Controllers\Backend\Admin\AdminController;
use App\Http\Controllers\Backend\Admin\RoleController;
use App\Http\Controllers\Backend\Admin\PermissionController;
use App\Http\Controllers\Backend\Menu\NavController;
use App\Http\Controllers\Backend\Menu\TagController;
use App\Http\Controllers\Backend\Menu\CategoryController;
use App\Http\Controllers\Backend\Content\ArticleController;
use App\Http\Controllers\Backend\Content\ColumnController;
use App\Http\Controllers\Backend\Content\BannerController;
use App\Http\Controllers\Backend\Content\SectionController;
use App\Http\Controllers\Backend\Content\CommentController;
use App\Http\Controllers\Backend\Attachment\ImageController;
use App\Http\Controllers\Backend\Attachment\TempController;
use App\Http\Controllers\Backend\Link\LinkController;
use App\Http\Controllers\Backend\Schema\SchemaTypeController;
use App\Http\Controllers\Backend\Schema\SchemaPropertyController;
use App\Http\Controllers\Backend\Acupoint\MeridianController;
use App\Http\Controllers\Backend\Acupoint\MeridianLineController;
use App\Http\Controllers\Backend\Acupoint\AcupointController;
use App\Http\Controllers\Backend\Acupoint\AcupointDescController;
use App\Http\Controllers\Backend\System\SiteInfoController;
use App\Http\Controllers\Backend\System\OptionController;
use App\Http\Controllers\Backend\Cache\CacheController;
use App\Http\Controllers\Backend\System\BackupController;

Route::get('/currentAdmin', [AuthController::class, 'currentadmin'])->middleware('auth:api-admin');
Route::post('/login/outLogin', [AuthController::class, 'adminOutLogin'])->middleware('auth:api-admin');
Route::post('/register', [AuthController::class, 'register']);



Route::namespace('Backend\Admin')->middleware('auth:api-admin')->group(function() {
  // 用户管理
  Route::prefix('customer/manage/admin')->middleware('permission:admin')->group(function() {
    Route::get('/', [AdminController::class, 'index'])->name('admin.list');
    // 数据表格接口
    //Route::get('data', [AdminController::class, 'data'])->name('admin.data');
    // 添加
    Route::post('store', [AdminController::class, 'store'])->middleware('permission:admin.store')->name('admin.store');
    // 编辑
    Route::put('{id}/update', [AdminController::class, 'update'])->middleware('permission:admin.update')->name('admin.update');
    // 删除 假删除
    Route::delete('destroy', [AdminController::class, 'destroy'])->name('admin.destroy');
    // 恢复
    Route::delete('restore', [AdminController::class, 'restore'])->name('admin.restore');
    // 删除 彻底删除
    Route::delete('force', [AdminController::class, 'force'])->middleware('permission:admin.destroy')->name('admin.force');
    // 获取角色
    Route::get('{id}/role', [AdminController::class, 'role'])->name('admin.role');
    // 赋予角色
    Route::put('{id}/assignRole', [AdminController::class, 'assignRole'])->middleware('permission:admin.assignRole')->name('admin.assignRole');
    // 获取权限
    Route::get('{id}/permission', [AdminController::class, 'permission'])->name('admin.permission');
    // 赋予权限
    Route::put('{id}/assignPermission', [AdminController::class, 'assignPermission'])->middleware('permission:admin.assignPermission')->name('admin.assignPermission');
  });
  
  Route::prefix('customer/manage/role')->middleware('permission:role')->group(function() {
    Route::get('/', [RoleController::class, 'index'])->name('role.list');
    Route::get('data', [RoleController::class, 'data'])->name('role.data');
    Route::post('store', [RoleController::class, 'store'])->middleware('permission:role.store')->name('role.store');
    Route::put('{id}/update', [RoleController::class, 'update'])->middleware('permission:role.update')->name('role.update');
    Route::delete('destroy', [RoleController::class, 'destroy'])->name('role.destroy');
    Route::delete('restore', [RoleController::class, 'restore'])->name('role.restore');
    Route::delete('force', [RoleController::class, 'force'])->middleware('permission:role.destroy')->name('role.force');
    Route::get('{id}/permission', [RoleController::class, 'permission'])->name('role.permission');
    Route::put('{id}/assignPermission', [RoleController::class, 'assignPermission'])->middleware('permission:role.assignPermission')->name('role.assignPermission');
  });
  
  Route::prefix('customer/manage/permission')->middleware('permission:permission')->group(function() {
    Route::get('/', [PermissionController::class, 'index'])->name('permission.list');
    Route::get('data', [PermissionController::class, 'data'])->name('permission.data');
    Route::post('store', [PermissionController::class, 'store'])->middleware('permission:permission.store')->name('permission.store');
    Route::put('{id}/update', [PermissionController::class, 'update'])->middleware('permission:permission.update')->name('permission.update');
    Route::delete('destroy', [PermissionController::class, 'destroy'])->name('permission.destroy');
    Route::delete('restore', [PermissionController::class, 'restore'])->name('permission.restore');
    Route::delete('force', [PermissionController::class, 'force'])->middleware('permission:permission.destroy')->name('permission.force');
  });
});

Route::namespace('Backend\Menu')->middleware('auth:api-admin')->group(function() {
  Route::prefix('sitedata/menu/nav')->middleware('permission:nav')->group(function() {
    Route::get('/', [NavController::class, 'index'])->name('nav.list');
    Route::get('data', [NavController::class, 'data'])->name('nav.data');
    Route::post('store', [NavController::class, 'store'])->middleware('permission:nav.store')->name('nav.store');
    Route::put('{id}/update', [NavController::class, 'update'])->middleware('permission:nav.update')->name('nav.update');
    Route::delete('destroy', [NavController::class, 'destroy'])->name('nav.destroy');
    Route::delete('restore', [NavController::class, 'restore'])->name('nav.restore');
    Route::delete('force', [NavController::class, 'force'])->middleware('permission:nav.destroy')->name('nav.force');
  });
  Route::prefix('sitedata/menu/tag')->middleware('permission:tag')->group(function() {
    Route::get('/', [TagController::class, 'index'])->name('tag.list');
    Route::get('data', [TagController::class, 'data'])->name('tag.data');
    Route::post('store', [TagController::class, 'store'])->middleware('permission:tag.store')->name('tag.store');
    Route::put('{id}/update', [TagController::class, 'update'])->middleware('permission:tag.update')->name('tag.update');
    Route::delete('destroy', [TagController::class, 'destroy'])->name('tag.destroy');
    Route::delete('restore', [TagController::class, 'restore'])->name('tag.restore');
    Route::delete('force', [TagController::class, 'force'])->middleware('permission:tag.destroy')->name('tag.force');
  });
  Route::prefix('sitedata/menu/category')->middleware('permission:category')->group(function() {
    Route::get('/', [CategoryController::class, 'index'])->name('category.list');
    Route::get('data', [CategoryController::class, 'data'])->name('category.data');
    Route::post('store', [CategoryController::class, 'store'])->middleware('permission:category.store')->name('category.store');
    Route::put('{id}/update', [CategoryController::class, 'update'])->middleware('permission:category.update')->name('category.update');
    Route::delete('destroy', [CategoryController::class, 'destroy'])->name('category.destroy');
    Route::delete('restore', [CategoryController::class, 'restore'])->name('category.restore');
    Route::delete('force', [CategoryController::class, 'force'])->middleware('permission:category.destroy')->name('category.force');
  });
});

Route::namespace('Backend\Content')->middleware('auth:api-admin')->group(function() {
  Route::prefix('sitedata/content/column')->middleware('permission:column')->group(function() {
    Route::get('/', [ColumnController::class, 'index'])->name('column.list');
    Route::get('data', [ColumnController::class, 'data'])->name('column.data');
    Route::post('store', [ColumnController::class, 'store'])->middleware('permission:column.store')->name('column.store');
    Route::put('{id}/update', [ColumnController::class, 'update'])->middleware('permission:column.update')->name('column.update');
    Route::delete('destroy', [ColumnController::class, 'destroy'])->name('column.destroy');
    Route::delete('restore', [ColumnController::class, 'restore'])->name('column.restore');
    Route::delete('force', [ColumnController::class, 'force'])->middleware('permission:column.destroy')->name('column.force');
  });
  Route::prefix('sitedata/content/section')->group(function() {
    Route::get('/', [SectionController::class, 'index'])->name('content.section');
    Route::get('data', [SectionController::class, 'data'])->name('content.section.data');
    Route::post('store', [SectionController::class, 'store'])->name('content.section.store');
    Route::put('{id}/update', [SectionController::class, 'update'])->name('content.section.update');
    Route::delete('destroy', [SectionController::class, 'destroy'])->name('content.section.destroy');
    Route::delete('restore', [SectionController::class, 'restore'])->name('content.section.restore');
    Route::delete('force', [SectionController::class, 'force'])->name('content.section.force');
    });
  Route::prefix('sitedata/content/article')->middleware('permission:article')->group(function() {
    Route::get('/', [ArticleController::class, 'index'])->name('article.list');
    Route::get('data', [ArticleController::class, 'data'])->name('article.data');
    Route::post('store', [ArticleController::class, 'store'])->middleware('permission:article.store')->name('article.store');
    Route::put('{id}/update', [ArticleController::class, 'update'])->middleware('permission:article.update')->name('article.update');
    Route::delete('destroy', [ArticleController::class, 'destroy'])->name('article.destroy');
    Route::delete('restore', [ArticleController::class, 'restore'])->name('article.restore');
    Route::delete('force', [ArticleController::class, 'force'])->middleware('permission:article.destroy')->name('article.force');
    Route::delete('baidu', [ArticleController::class, 'baidu'])->name('article.destroy');
  });
  Route::prefix('sitedata/content/banner')->middleware('permission:banner')->group(function() {
    Route::get('/', [BannerController::class, 'index'])->name('banner.list');
    Route::get('data', [BannerController::class, 'data'])->name('banner.data');
    Route::post('store', [BannerController::class, 'store'])->middleware('permission:banner.store')->name('banner.store');
    Route::put('{id}/update', [BannerController::class, 'update'])->middleware('permission:banner.update')->name('banner.update');
    Route::delete('destroy', [BannerController::class, 'destroy'])->name('banner.destroy');
    Route::delete('restore', [BannerController::class, 'restore'])->name('banner.restore');
    Route::delete('force', [BannerController::class, 'force'])->middleware('permission:banner.destroy')->name('banner.force');
  });
  Route::prefix('sitedata/content/comment')->group(function() {
    Route::get('/', [CommentController::class, 'index'])->name('comment.list');
    Route::post('store', [CommentController::class, 'store'])->name('comment.store');
    Route::put('{id}/update', [CommentController::class, 'update'])->name('comment.update');
    Route::delete('destroy', [CommentController::class, 'destroy'])->name('comment.destroy');
    Route::delete('restore', [CommentController::class, 'restore'])->name('comment.restore');
    Route::delete('force', [CommentController::class, 'force'])->name('comment.force');
  });
});

Route::namespace('Backend\Attachment')->middleware('auth:api-admin')->group(function() {
    Route::prefix('sitedata/attachment/image')->group(function() {
        Route::get('/', [ImageController::class, 'index']);
        Route::get('data', [ImageController::class, 'data']);
        Route::post('store', [ImageController::class, 'store']);
        Route::put('{id}/update', [ImageController::class, 'update']);
        Route::delete('destroy', [ImageController::class, 'destroy']);
        Route::delete('restore', [ImageController::class, 'restore']);
        Route::delete('force', [ImageController::class, 'force']);
    });
    Route::prefix('sitedata/attachment/temp')->group(function() {
        Route::get('/', [TempController::class, 'index']);
        Route::post('store', [TempController::class, 'store']);
        Route::delete('destroy', [TempController::class, 'destroy']);
        
    });
});
Route::namespace('Backend\Link')->middleware('auth:api-admin')->group(function() {
    Route::prefix('sitedata/link')->group(function() {
        Route::get('/', [LinkController::class, 'index']);
        Route::get('data', [LinkController::class, 'data']);
        Route::post('store', [LinkController::class, 'store']);
        Route::put('{id}/update', [LinkController::class, 'update']);
        Route::delete('destroy', [LinkController::class, 'destroy']);
        Route::delete('restore', [LinkController::class, 'restore']);
        Route::delete('force', [LinkController::class, 'force']);
    });
});

Route::namespace('Backend\Schema')->middleware('auth:api-admin')->group(function() {
    // Schema 类型管理
    Route::prefix('schema/type')->group(function() {
        Route::get('/', [SchemaTypeController::class, 'index'])->name('schema.type.list');
        Route::get('data', [SchemaTypeController::class, 'data'])->name('schema.type.data');
        Route::get('{id}/show', [SchemaTypeController::class, 'show'])->name('schema.type.show');
        Route::post('store', [SchemaTypeController::class, 'store'])->name('schema.type.store');
        Route::put('{id}/update', [SchemaTypeController::class, 'update'])->name('schema.type.update');
        Route::delete('destroy', [SchemaTypeController::class, 'destroy'])->name('schema.type.destroy');
        Route::delete('restore', [SchemaTypeController::class, 'restore'])->name('schema.type.restore');
        Route::delete('force', [SchemaTypeController::class, 'force'])->name('schema.type.force');
    });
    // Schema 属性管理
    Route::prefix('schema/property')->group(function() {
        Route::get('/', [SchemaPropertyController::class, 'index'])->name('schema.property.list');
        Route::get('data', [SchemaPropertyController::class, 'data'])->name('schema.property.data');
        Route::get('{id}/show', [SchemaPropertyController::class, 'show'])->name('schema.property.show');
        Route::post('store', [SchemaPropertyController::class, 'store'])->name('schema.property.store');
        Route::put('{id}/update', [SchemaPropertyController::class, 'update'])->name('schema.property.update');
        Route::delete('destroy', [SchemaPropertyController::class, 'destroy'])->name('schema.property.destroy');
        Route::delete('restore', [SchemaPropertyController::class, 'restore'])->name('schema.property.restore');
        Route::delete('force', [SchemaPropertyController::class, 'force'])->name('schema.property.force');
    });
});

Route::namespace('Backend\Acupoint')->middleware('auth:api-admin')->group(function() {
    // 经脉管理
    Route::prefix('acupoint/meridian')->group(function() {
        Route::get('/', [MeridianController::class, 'index'])->name('acupoint.meridian.list');
        Route::get('data', [MeridianController::class, 'data'])->name('acupoint.meridian.data');
        Route::get('{id}/show', [MeridianController::class, 'show'])->name('acupoint.meridian.show');
        Route::post('store', [MeridianController::class, 'store'])->name('acupoint.meridian.store');
        Route::put('{id}/update', [MeridianController::class, 'update'])->name('acupoint.meridian.update');
        Route::delete('destroy', [MeridianController::class, 'destroy'])->name('acupoint.meridian.destroy');
        Route::delete('restore', [MeridianController::class, 'restore'])->name('acupoint.meridian.restore');
        Route::delete('force', [MeridianController::class, 'force'])->name('acupoint.meridian.force');
    });
    // 经脉线管理
    Route::prefix('acupoint/meridian_line')->group(function() {
        Route::get('/', [MeridianLineController::class, 'index'])->name('acupoint.meridian_line.list');
        Route::get('data', [MeridianLineController::class, 'data'])->name('acupoint.meridian_line.data');
        Route::get('{id}/show', [MeridianLineController::class, 'show'])->name('acupoint.meridian_line.show');
        Route::post('store', [MeridianLineController::class, 'store'])->name('acupoint.meridian_line.store');
        Route::put('{id}/update', [MeridianLineController::class, 'update'])->name('acupoint.meridian_line.update');
        Route::delete('destroy', [MeridianLineController::class, 'destroy'])->name('acupoint.meridian_line.destroy');
        Route::delete('restore', [MeridianLineController::class, 'restore'])->name('acupoint.meridian_line.restore');
        Route::delete('force', [MeridianLineController::class, 'force'])->name('acupoint.meridian_line.force');
    });
    // 穴道管理
    Route::prefix('acupoint/acupoint')->group(function() {
        Route::get('/', [AcupointController::class, 'index'])->name('acupoint.acupoint.list');
        Route::get('data', [AcupointController::class, 'data'])->name('acupoint.acupoint.data');
        Route::get('{id}/show', [AcupointController::class, 'show'])->name('acupoint.acupoint.show');
        Route::post('store', [AcupointController::class, 'store'])->name('acupoint.acupoint.store');
        Route::put('{id}/update', [AcupointController::class, 'update'])->name('acupoint.acupoint.update');
        Route::delete('destroy', [AcupointController::class, 'destroy'])->name('acupoint.acupoint.destroy');
        Route::delete('restore', [AcupointController::class, 'restore'])->name('acupoint.acupoint.restore');
        Route::delete('force', [AcupointController::class, 'force'])->name('acupoint.acupoint.force');
    });
    // 穴位介绍管理
    Route::prefix('acupoint/desc')->group(function() {
        Route::get('/', [AcupointDescController::class, 'index'])->name('acupoint.desc.list');
        Route::get('data', [AcupointDescController::class, 'data'])->name('acupoint.desc.data');
        Route::get('{id}/show', [AcupointDescController::class, 'show'])->name('acupoint.desc.show');
        Route::post('store', [AcupointDescController::class, 'store'])->name('acupoint.desc.store');
        Route::put('{id}/update', [AcupointDescController::class, 'update'])->name('acupoint.desc.update');
        Route::delete('destroy', [AcupointDescController::class, 'destroy'])->name('acupoint.desc.destroy');
        Route::delete('restore', [AcupointDescController::class, 'restore'])->name('acupoint.desc.restore');
        Route::delete('force', [AcupointDescController::class, 'force'])->name('acupoint.desc.force');
    });
});

Route::namespace('Backend\system')->middleware('auth:api-admin')->group(function() {
    Route::prefix('system/setting')->group(function() {
        Route::get('/', [SiteInfoController::class, 'index'])->name('webinfo.list');
        Route::get('data', [SiteInfoController::class, 'data'])->name('webinfo.data');
        Route::post('store', [SiteInfoController::class, 'store'])->middleware('permission:basic.store')->name('webinfo.store');
        Route::put('{id}/update', [SiteInfoController::class, 'update'])->middleware('permission:basic.update')->name('webinfo.update');
        Route::delete('destroy', [SiteInfoController::class, 'destroy'])->name('webinfo.destroy');
        Route::delete('restore', [SiteInfoController::class, 'restore'])->name('webinfo.restore');
        Route::delete('force', [SiteInfoController::class, 'force'])->middleware('permission:basic.destroy')->name('webinfo.force');
    
        Route::prefix('option')->group(function() {
            Route::get('/', [OptionController::class, 'index'])->name('webinfo.option.list');
            Route::get('data', [OptionController::class, 'data'])->name('webinfo.option.data');
            Route::post('store', [OptionController::class, 'store'])->middleware('permission:basic.store')->name('webinfo.option.store');
            Route::put('{id}/update', [OptionController::class, 'update'])->middleware('permission:basic.update')->name('webinfo.option.update');
            Route::delete('{id}/delete', [OptionController::class, 'destroy'])->name('webinfo.option.destroy');
        });
    });
});

 Route::prefix('system/cache')->group(function () {
    Route::post('clear',        [CacheController::class, 'clear']);
    Route::post('clear/{type}', [CacheController::class, 'clearByType']);
    Route::get('status',        [CacheController::class, 'status']);
 });

Route::prefix('system/backup')->group(function () {
    Route::get('',                 [BackupController::class, 'index']);
    Route::post('store',           [BackupController::class, 'store']);
    Route::get('download/{id}',    [BackupController::class, 'download']);
    Route::delete('delete/{id}',   [BackupController::class, 'destroy']);
    Route::post('restore/{id}',    [BackupController::class, 'restore']);
    Route::get('schedule',         [BackupController::class, 'getSchedule']);
    Route::put('schedule',         [BackupController::class, 'saveSchedule']);
 });

// Route::middleware('auth:admin')->group(function () {
    // Route::get('/profile', function (Request $request) {
        // return $request->user();
    // });

    // Route::get('/activities', function (Request $request) {
        // return $request->user()->activities()->latest()->paginate(20);
    // });
// });
