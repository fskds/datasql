<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PermissionsSeeder extends Seeder
{
    /**
     * 运行权限数据填充
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('admin_permissions')->truncate();
        DB::statement('ALTER TABLE admin_permissions AUTO_INCREMENT = 1');
        Schema::enableForeignKeyConstraints();

        $guardName = 'api-admin';
        $now = now();

        // 终极子菜单及其按钮配置：[pid, menu_name, [buttons]]
        $buttonConfigs = [
            // 客户管理 - 管理员
            [3, 'admin', ['store', 'update', 'destroy', 'assignRole', 'assignPermission']],
            [4, 'role', ['store', 'update', 'destroy', 'assignPermission']],
            [5, 'permission', ['store', 'update', 'destroy']],
            // 客户管理 - 会员
            [7, 'user', ['store', 'update', 'destroy']],
            [8, 'memberRole', ['store', 'update', 'destroy']],
            [9, 'level', ['store', 'update', 'destroy']],
            // 客户管理 - 日志（仅 destroy）
            [11, 'loginLog', ['destroy']],
            [12, 'operationLog', ['destroy']],
            // 站点数据 - 菜单管理
            [15, 'nav', ['store', 'update', 'destroy']],
            [16, 'category', ['store', 'update', 'destroy']],
            [17, 'tag', ['store', 'update', 'destroy']],
            // 站点数据 - 内容管理
            [19, 'column', ['store', 'update', 'destroy']],
            [20, 'article', ['store', 'update', 'destroy']],
            [21, 'comment', ['store', 'update', 'destroy']],
            [22, 'banner', ['store', 'update', 'destroy']],
            // 站点数据 - 附件管理
            [24, 'image', ['store', 'update', 'destroy']],
            // 站点数据 - 友情链接
            [25, 'link', ['store', 'update', 'destroy']],
            // 系统管理 - 系统设置
            [28, 'basic', ['store', 'update', 'destroy']],
            [29, 'contact', ['store', 'update', 'destroy']],
            [30, 'email', ['store', 'update', 'destroy']],
            [31, 'sms', ['store', 'update', 'destroy']],
            [32, 'upload', ['store', 'update', 'destroy']],
            [33, 'watermark', ['store', 'update', 'destroy']],
            [34, 'seo', ['store', 'update', 'destroy']],
            // 系统管理 - 缓存管理（无按钮）
            // 系统管理 - 数据备份
            [36, 'backup', ['store', 'update', 'destroy']],
        ];

        // 按钮显示名称映射
        $buttonLabels = [
            'store' => '新增',
            'update' => '修改',
            'destroy' => '删除',
            'assignRole' => '分配角色',
            'assignPermission' => '分配权限',
        ];

        // 按钮图标映射
        $buttonIcons = [
            'store' => 'plus',
            'update' => 'edit',
            'destroy' => 'delete',
            'assignRole' => 'team',
            'assignPermission' => 'safety',
        ];

        // 菜单类型权限
        $menus = [
            // id, display_name, name, route, icon, pid, description, sort
            // 客户管理
            [1, '客户管理', 'customer', '/customer', 'team', 0, '客户管理模块', 10],
            [2, '管理员', 'manage', '/customer/admin', 'user', 1, '管理员管理', 0],
            [3, '管理员', 'admin', '/customer/admin/admin', null, 2, '管理员用户管理', 0],
            [4, '角色', 'role', '/customer/admin/role', null, 2, '角色管理', 1],
            [5, '权限', 'permission', '/customer/admin/permission', null, 2, '权限管理', 2],
            [6, '会员', 'member', '/customer/member', 'user', 1, '会员管理', 1],
            [7, '用户', 'user', '/customer/member/user', null, 6, '会员用户管理', 0],
            [8, '角色', 'memberRole', '/customer/member/role', null, 6, '会员角色管理', 1],
            [9, '等级', 'level', '/customer/member/level', null, 6, '会员等级管理', 2],
            [10, '日志', 'log', '/customer/log', 'file-text', 1, '日志管理', 2],
            [11, '登录日志', 'loginLog', '/customer/log/login', null, 10, '登录日志查看', 0],
            [12, '操作日志', 'operationLog', '/customer/log/operation', null, 10, '操作日志查看', 1],

            // 站点数据
            [13, '站点数据', 'sitedata', '/sitedata', 'file-text', 0, '站点数据管理', 20],
            [14, '菜单管理', 'menu', '/sitedata/menu', 'menu', 13, '菜单管理', 0],
            [15, '导航', 'nav', '/sitedata/menu/nav', null, 14, '导航管理', 0],
            [16, '栏目', 'category', '/sitedata/menu/category', null, 14, '栏目管理', 1],
            [17, '标签', 'tag', '/sitedata/menu/tag', null, 14, '标签管理', 2],
            [18, '内容管理', 'content', '/sitedata/content', 'container', 13, '内容管理', 1],
            [19, '栏目', 'column', '/sitedata/content/column', null, 18, '内容栏目管理', 0],
            [20, '文章', 'article', '/sitedata/content/article', null, 18, '文章管理', 1],
            [21, '评论', 'comment', '/sitedata/content/comment', null, 18, '评论管理', 2],
            [22, '轮播图', 'banner', '/sitedata/content/banner', null, 18, '轮播图管理', 3],
            [23, '附件管理', 'attachment', '/sitedata/attachment', 'folder', 13, '附件管理', 2],
            [24, '图片', 'image', '/sitedata/attachment/image', null, 23, '图片管理', 0],
            [25, '友情链接', 'link', '/sitedata/link', 'link', 13, '友情链接管理', 3],

            // 系统管理
            [26, '系统管理', 'system', '/system', 'setting', 0, '系统管理', 100],
            [27, '系统设置', 'setting', '/system/setting', 'tool', 26, '系统设置', 0],
            [28, '基础配置', 'basic', '/system/setting/basic', null, 27, '基础配置', 0],
            [29, '联系方式', 'contact', '/system/setting/contact', null, 27, '联系方式配置', 1],
            [30, '邮箱服务', 'email', '/system/setting/email', null, 27, '邮箱服务配置', 2],
            [31, '短信设置', 'sms', '/system/setting/sms', null, 27, '短信设置', 3],
            [32, '上传设置', 'upload', '/system/setting/upload', null, 27, '上传设置', 4],
            [33, '水印设置', 'watermark', '/system/setting/watermark', null, 27, '水印设置', 5],
            [34, 'SEO设置', 'seo', '/system/settings/seo', null, 27, 'SEO设置', 6],
            [35, '缓存管理', 'cache', '/system/cache', 'cloud', 26, '缓存管理', 1],
            [36, '数据备份', 'backup', '/system/backup', 'database', 26, '数据备份', 2],
        ];

        $permissions = [];

        // 组装菜单权限
        foreach ($menus as [$id, $displayName, $name, $route, $icon, $pid, $description, $sort]) {
            $permissions[] = [
                'id' => $id,
                'display_name' => $displayName,
                'name' => $name,
                'guard_name' => $guardName,
                'route' => $route,
                'icon' => $icon,
                'pid' => $pid,
                'type' => 'menu',
                'description' => $description,
                'sort' => $sort,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // 组装按钮权限
        $buttonId = 37; // 从 37 开始，与菜单权限 id=36 衔接
        foreach ($buttonConfigs as [$pid, $menuName, $buttons]) {
            $sort = 0;
            foreach ($buttons as $btn) {
                $permissions[] = [
                    'id' => $buttonId++,
                    'display_name' => $buttonLabels[$btn],
                    'name' => $menuName . '.' . $btn,
                    'guard_name' => $guardName,
                    'route' => null,
                    'icon' => $buttonIcons[$btn],
                    'pid' => $pid,
                    'type' => 'button',
                    'description' => $buttonLabels[$btn],
                    'sort' => $sort++,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('admin_permissions')->insert($permissions);

        $this->command->info('权限数据填充完成！共 ' . count($permissions) . ' 条记录。');
    }
}
