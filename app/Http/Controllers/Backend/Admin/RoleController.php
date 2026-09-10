<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Admin\Admin;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\RoleCreateRequest;
use App\Http\Requests\Admin\RoleUpdateRequest;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $current = max(1, (int)$request->get('current', 1));
        $pageSize = max(1, (int)$request->get('pageSize', 10));

        $query = Role::select('id', 'name', 'code', 'description', 'status');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->model === 'hasdel') {
            $query->onlyTrashed();
        }

        $res = $query->paginate($pageSize, ['*'], 'page', $current);

        return response()->json([
            'status' => true,
            'data' => $res->items(),
            'total' => $res->total(),
            'pageSize' => $res->perPage(),
            'current' => $res->currentPage(),
        ]);
    }

    public function store(RoleCreateRequest $request)
    {
        $data = $request->validated();
        $data['display_name'] = $data['name'];

        if (Role::create($data)) {
            return response()->json(['status' => true, 'msg' => '添加角色成功']);
        }

        return response()->json(['status' => false, 'msg' => '添加失败，请联系管理员'], 500);
    }

    public function update(RoleUpdateRequest $request, $id)
    {
        $role = Role::findOrFail($id);
        $data = $request->validated();

        if ($role->update($data)) {
            return response()->json(['status' => true, 'msg' => '修改角色成功']);
        }

        return response()->json(['status' => false, 'msg' => '更新失败，请联系管理员'], 500);
    }

    public function destroy(Request $request)
    {
        $ids = (array)$request->get('ids', []);

        if (empty($ids)) {
            return response()->json(['status' => false, 'msg' => '请选择删除项']);
        }

        if (in_array(1, $ids)) {
            return response()->json(['status' => false, 'msg' => '不可删除超级管理员角色']);
        }

        if (Role::destroy($ids)) {
            return response()->json(['status' => true, 'msg' => '删除角色成功']);
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
            $role = Role::withTrashed()->find($id);
            if ($role) {
                $role->restore();
            }
        }

        return response()->json(['status' => true, 'msg' => '恢复角色成功']);
    }

    public function force(Request $request)
    {
        $ids = (array)$request->get('ids', []);

        if (empty($ids)) {
            return response()->json(['status' => false, 'msg' => '请选择删除项']);
        }

        foreach ($ids as $id) {
            $role = Role::withTrashed()->find($id);
            if ($role) {
                $admins = Admin::all();
                foreach ($admins as $admin) {
                    $admin->removeRole($role);
                }
                $role->syncPermissions([]);
                $role->forceDelete();
            }
        }

        return response()->json(['status' => true, 'msg' => '强制删除角色成功']);
    }

    public function data()
    {
        $roles = Role::select('id', 'name')
            ->get()
            ->map(fn($role) => [
                'label' => $role->name,
                'value' => $role->id,
            ]);

        return response()->json(['status' => true, 'allRole' => $roles]);
    }

    public function permission(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $permissionIds = $role->permissions->pluck('id');

        return response()->json([
            'status' => true,
            'dPermission' => $permissionIds,
        ]);
    }

    public function assignPermission(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $permissions = (array)$request->get('permissions', []);
        $permissionIds = array_map('intval', $permissions);

        if (empty($permissionIds)) {
            $role->permissions()->detach();
            return response()->json(['status' => true, 'msg' => '更新角色权限成功']);
        }

        $role->syncPermissions($permissionIds);

        return response()->json(['status' => true, 'msg' => '更新角色权限成功']);
    }
}