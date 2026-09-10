<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Admin\Admin;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\AdminCreateRequest;
use App\Http\Requests\Admin\AdminUpdateRequest;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Admin::select('id', 'username', 'mobile', 'name', 'email', 'status')
            ->with(['roles']);

        if ($request->filled('username')) {
            $query->where('username', 'like', '%' . $request->username . '%');
        }

        if ($request->model === 'hasdel') {
            $query->onlyTrashed();
        }

        $current = max(1, (int)$request->get('current', 1));
        $pageSize = max(1, (int)$request->get('pageSize', 10));

        $res = $query->paginate($pageSize, ['*'], 'page', $current);

        return response()->json([
            'status' => true,
            'data' => $res->items(),
            'total' => $res->total(),
            'pageSize' => $res->perPage(),
            'current' => $res->currentPage(),
        ]);
    }

    public function store(AdminCreateRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        if (Admin::create($data)) {
            return response()->json(['status' => true, 'msg' => '添加管理员成功']);
        }

        return response()->json(['status' => false, 'msg' => '添加失败，请联系管理员'], 500);
    }

    public function update(AdminUpdateRequest $request, $id)
    {
        $admin = Admin::findOrFail($id);
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        if ($admin->update($data)) {
            if (!empty($data['password']) && Auth::guard('api-admin')->user()->id == $id) {
                return response()->json(['status' => true, 'msg' => '成功更新管理员密码']);
            }
            return response()->json(['status' => true, 'msg' => '成功更新管理员']);
        }

        return response()->json(['status' => false, 'msg' => '更新失败，请联系管理员'], 500);
    }

    public function destroy(Request $request)
    {
        $ids = (array)$request->get('ids', []);

        if (empty($ids)) {
            return response()->json(['status' => false, 'msg' => '请选择删除项']);
        }

        $currentAdminId = Auth::guard('api-admin')->id();

        if (in_array($currentAdminId, $ids) || in_array(1, $ids)) {
            return response()->json(['status' => false, 'msg' => '不可删除超级管理员或自己']);
        }

        if (Admin::destroy($ids)) {
            return response()->json(['status' => true, 'msg' => '删除成功']);
        }

        return response()->json(['status' => false, 'msg' => '删除失败'], 500);
    }

    public function restore(Request $request)
    {
        $ids = (array)$request->get('ids', []);

        if (empty($ids)) {
            return response()->json(['status' => false, 'msg' => '请选择恢复项']);
        }

        foreach ($ids as $id) {
            $admin = Admin::withTrashed()->find($id);
            if ($admin) {
                $admin->restore();
            }
        }

        return response()->json(['status' => true, 'msg' => '恢复成功']);
    }

    public function force(Request $request)
    {
        $ids = (array)$request->get('ids', []);

        if (empty($ids)) {
            return response()->json(['status' => false, 'msg' => '请选择删除项']);
        }

        foreach ($ids as $id) {
            $admin = Admin::withTrashed()->find($id);
            if ($admin) {
                $admin->forceDelete();
            }
        }

        return response()->json(['status' => true, 'msg' => '强制删除成功']);
    }

    public function role(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);
        $roleIds = $admin->roles()->pluck('id');

        return response()->json([
            'status' => true,
            'data' => $roleIds,
        ]);
    }

    public function assignRole(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);
        $roles = (array)$request->get('roles', []);
        $roleIds = array_map('intval', $roles);

        $admin->syncRoles($roleIds);

        return response()->json(['status' => true, 'msg' => '更新角色成功']);
    }

    public function permission(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $directPermissionIds = $admin->getDirectPermissions()->pluck('id');
        $rolePermissionIds = $admin->getPermissionsViaRoles()->pluck('id');
        $allPermissionIds = $directPermissionIds->merge($rolePermissionIds)->unique();

        return response()->json([
            'status' => true,
            'aPermission' => $allPermissionIds,
            'dPermission' => $directPermissionIds,
        ]);
    }

    public function assignPermission(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);
        $permissions = (array)$request->get('permissions', []);
        $permissionIds = array_map('intval', $permissions);

        if (empty($permissionIds)) {
            $admin->permissions()->detach();
            return response()->json(['status' => true, 'msg' => '已更新用户直接权限']);
        }

        $admin->syncPermissions($permissionIds);

        return response()->json(['status' => true, 'msg' => '已更新用户直接权限']);
    }
}