<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminInitSeeder extends Seeder
{
    /**
     * 创建超级管理员用户、超级角色，并赋予所有权限
     */
    public function run(): void
    {
        $now = now();
        $guardName = 'api-admin';
        $userModelClass = 'App\\Models\\Admin\\Admin';

        // 1. 创建超级管理员用户
        $user = DB::table('admin_users')->where('username', 'root')->first();

        if (! $user) {
            $userId = DB::table('admin_users')->insertGetId([
                'username' => 'root',
                'mobile' => '13800000000',
                'name' => '超级管理员',
                'email' => 'a@a.com',
                'password' => Hash::make('123456'),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $this->command->info("已创建超级管理员用户 (id: $userId)");
        } else {
            $userId = $user->id;
            $this->command->info("超级管理员用户已存在 (id: $userId)，跳过创建。");
        }

        // 2. 创建超级角色
        $role = DB::table('admin_roles')
            ->where('name', 'root')
            ->where('guard_name', $guardName)
            ->first();

        if (! $role) {
            $roleId = DB::table('admin_roles')->insertGetId([
                'guard_name' => $guardName,
                'name' => 'root',
                'display_name' => '超级管理员',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $this->command->info("已创建超级角色 (id: $roleId)");
        } else {
            $roleId = $role->id;
            $this->command->info("超级角色已存在 (id: $roleId)，跳过创建。");
        }

        // 3. 获取所有权限 id
        $permissionIds = DB::table('admin_permissions')->pluck('id')->toArray();

        if (empty($permissionIds)) {
            $this->command->warn('permissions 表中暂无权限数据，跳过权限分配。');

            return;
        }

        $this->command->info('共发现 '.count($permissionIds).' 条权限，开始分配...');

        // 4. 清空并重新分配：角色 <-> 权限 (role_has_permissions)
        if (Schema::hasTable('admin_role_has_permissions')) {
            DB::table('admin_role_has_permissions')->where('role_id', $roleId)->delete();

            $rolePermissionRows = array_map(function ($pid) use ($roleId) {
                return ['permission_id' => $pid, 'role_id' => $roleId];
            }, $permissionIds);

            DB::table('admin_role_has_permissions')->insert($rolePermissionRows);
            $this->command->info('已将所有 ' . count($permissionIds) . ' 条权限分配给 root 角色。');
        } else {
            $this->command->warn('未找到 role_has_permissions 表，跳过角色权限分配。');
        }

        // 5. 清空并重新分配：用户 <-> 角色 (model_has_roles)
        if (Schema::hasTable('admin_model_has_roles')) {
            DB::table('admin_model_has_roles')
                ->where('role_id', $roleId)
                ->where('model_id', $userId)
                ->where('model_type', $userModelClass)
                ->delete();

            DB::table('admin_model_has_roles')->insert([
                'role_id' => $roleId,
                'model_type' => $userModelClass,
                'model_id' => $userId,
            ]);
            $this->command->info('已将 root 角色分配给 root 用户。');
        } else {
            $this->command->warn('未找到 model_has_roles 表，跳过用户角色分配。');
        }

        // 6. 清空并重新分配：用户 <-> 权限 (model_has_permissions)
        //    （可选：如果你的项目主要依赖「用户->角色->权限」的方式，可省略此步。）
        if (Schema::hasTable('admin_model_has_permissions')) {
            DB::table('admin_model_has_permissions')
                ->where('model_type', $userModelClass)
                ->where('model_id', $userId)
                ->delete();

            $userPermissionRows = array_map(function ($pid) use ($userId, $userModelClass) {
                return [
                    'permission_id' => $pid,
                    'model_type' => $userModelClass,
                    'model_id' => $userId,
                ];
            }, $permissionIds);

            DB::table('admin_model_has_permissions')->insert($userPermissionRows);
            $this->command->info('已将所有 ' . count($permissionIds) . ' 条权限直接分配给 root 用户。');
        } else {
            $this->command->warn('未找到 model_has_permissions 表，跳过用户权限直接分配。');
        }

        $this->command->info('初始化完成！登录账号：root / 123456');
    }
}
