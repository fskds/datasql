<?php

namespace App\Http\Controllers\Backend\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\Admin;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    /**
     * Register a new user.
     */

    public function currentadmin()
    {
        if (Auth::check()) {
            //$user = auth()->user();
            $user = Auth::user();
            $data['name'] = $user->username;
            $data['avatar'] = "https://gw.alipayobjects.com/zos/antfincdn/XAosXuNZyF/BiazfanxmamNRoxxVxka.png";
            $data['userid'] = $user->id;
            $data['email'] = $user->email;
            $data['signature'] = $user->name;
            $data['title'] = $user->name;
            $data['group'] = $user->name;
            $data['unreadCount'] = $user->name;
            $data['access'] = $user->roles;
            $data['country'] = $user->name;
            $data['address'] = $user->name;
            $data['phone'] = $user->phone;

        return response()->json(['success' => true, 'data' => $data]);
        //return $data;
        } else {
            $data['isLogin'] = false;
            return response()->json(['success' => true, 'errorCode' =>'401', 'errorMessage' => '请先登录！', $data]);
           // return "未登录";
        }
    }
    public function adminOutLogin(Request $request): JsonResponse
    {
        $user = Auth::guard('api-admin')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'msg' => '未登录或登录已失效',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // 撤销当前令牌（同时撤销关联的刷新令牌）
        $accessToken = $user->token();

        if ($accessToken) {
            $accessToken->revoke();
        }

        return response()->json([
            'status' => true,
            'msg' => '退出登录成功',
        ]);
    }




    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:admin_users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admin_users,email'],
            'password' => ['required', 'string', 'min:6'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $admin = Admin::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'name' => $validated['name'] ?? $validated['username'],
            'status' => 1,
        ]);

        return response()->json([
            'status' => true,
            'msg' => '注册成功',
        ], 201);
    }

    /**
     * Get the authenticated user.
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }
}