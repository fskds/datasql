<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Admin\Admin;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PermissionCreateRequest;
use App\Http\Requests\Admin\PermissionUpdateRequest;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $model = $request->get('model');
        $query = Permission::query();

        if ($request->filled('name')) {
            $model = 'search';
        }

        switch (strtolower($model)) {
            case 'hasdel':
                $res = $query->onlyTrashed()->get();
                break;
            case 'search':
                $res = $query->where('name', 'like', '%' . $request->name . '%')->get();
                break;
            default:
                $query = $query->where('status', 1)->get();
                $res = $this->getPermissionTree($query, 0);
                break;
        }

        return response()->json([
            'status' => true,
            'data' => $res,
        ]);
    }

    public function store(PermissionCreateRequest $request)
    {
        $data = $request->validated();

        if (Permission::create($data)) {
            return response()->json(['status' => true, 'msg' => '成功添加权限']);
        }

        return response()->json(['status' => false, 'msg' => '添加失败，请联系管理员'], 500);
    }

    public function update(PermissionUpdateRequest $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $data = $request->validated();

        if ($permission->update($data)) {
            return response()->json(['status' => true, 'msg' => '成功更新权限']);
        }

        return response()->json(['status' => false, 'msg' => '更新失败，请联系管理员'], 500);
    }

    public function destroy(Request $request)
    {
        $ids = (array)$request->get('ids', []);

        if (empty($ids)) {
            return response()->json(['status' => false, 'msg' => '请选择删除项']);
        }

        if (Permission::destroy($ids)) {
            return response()->json(['status' => true, 'msg' => '成功删除权限']);
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
            $permission = Permission::withTrashed()->find($id);
            if ($permission) {
                $permission->restore();
            }
        }

        return response()->json(['status' => true, 'msg' => '成功恢复权限']);
    }

    public function force(Request $request)
    {
        $ids = (array)$request->get('ids', []);

        if (empty($ids)) {
            return response()->json(['status' => false, 'msg' => '请选择删除项']);
        }

        foreach ($ids as $id) {
            $permission = Permission::withTrashed()->find($id);
            if (!$permission) {
                continue;
            }

            $pchildren = Permission::withTrashed()->where('pid', $id)->count();
            if ($pchildren > 0) {
                return response()->json(['status' => false, 'msg' => '子权限不为空，无法删除']);
            }

            $admins = Admin::all();
            foreach ($admins as $admin) {
                $admin->revokePermissionTo($permission);
            }

            $roles = Role::all();
            foreach ($roles as $role) {
                $role->revokePermissionTo($permission);
            }

            $permission->forceDelete();
        }

        return response()->json(['status' => true, 'msg' => '成功强制删除权限']);
    }

    public function data(Request $request)
    {
        $query = Permission::where('status', 1)->get();
        $res = $this->getPermissionDataTree($query, 0);

        return response()->json(['status' => true, 'allPermission' => $res]);
    }

    private function getPermissionTree($data, $pid = 0)
    {
        $arr = [];
        foreach ($data as $value) {
            if ($value['pid'] == $pid) {
                $value['children'] = $this->getPermissionTree($data, $value['id']);
                if (empty($value['children'])) {
                    unset($value['children']);
                }
                $arr[] = $value;
            }
        }
        return $arr;
    }

    private function getPermissionDataTree($data, $pid = 0)
    {
        $arr = [];
        foreach ($data as $value) {
            if ($value['pid'] == $pid) {
                $ar = [
                    'title' => $value['display_name'],
                    'key' => $value['id'],
                ];
                $ar['children'] = $this->getPermissionDataTree($data, $value['id']);
                if (empty($ar['children'])) {
                    unset($ar['children']);
                }
                $arr[] = $ar;
            }
        }
        return $arr;
    }
}