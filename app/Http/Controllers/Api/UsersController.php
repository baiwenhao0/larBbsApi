<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Image;
use App\Http\Resources\UserResource;
use App\Http\Requests\Api\UserRequest;
use Illuminate\Auth\AuthenticationException;

class UsersController extends Controller
{
    public function store(UserRequest $request)
    {
        $verifyData = \Cache::get($request->verification_key);

        if (!$verifyData) {
            abort(403, '验证码已失效');
        }
        //hash_equals 是可防止时序攻击的字符串比较，那么什么是时序攻击呢？比如这段代码我们使用
//        $verifyData['code'] == $request->verification_code
        //进行比较，那么两个字符串是从第一位开始逐一进行比较的，发现不同就立即返回 false，那么通过计算返回的速度就知道了大概是哪一位开始不同的，这样就实现了电影中经常出现的按位破解密码的场景。而使用 hash_equals 比较两个字符串，无论字符串是否相等，函数的时间消耗是恒定的，这样可以有效的防止时序攻击。

        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            // 返回401
            throw new AuthenticationException('验证码错误');
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $verifyData['phone'],
            'password' => $request->password,
        ]);

        // 清除验证码缓存
        \Cache::forget($request->verification_key);
        return (new UserResource($user))->showSensitiveFields(); //API接口
        return new UserResource($user);
    }

    public function update(UserRequest $request)
    {
        $user = $request->user();
        $attributes = $request->only(['name', 'email', 'introduction','registration_id']);
        if ($request->avatar_image_id) {
            $image = Image::find($request->avatar_image_id);
            $attributes['avatar'] = $image->path;
        }
        $user->update($attributes);
        return (new UserResource($user))->showSensitiveFields();
    }
    //API 带jwt 令牌路由
    public function show(User $user, Request $request)
    {
        return new UserResource($user);
    }

    public function me(Request $request)
    {
        return (new UserResource($request->user()))->showSensitiveFields();
        return new UserResource($request->user());
    }
//活跃用户的逻辑代码放置于在 Trait —— app/Models/Traits/ActiveUserHelper.php 中，算法的讲解，代码里有注释，这里便不再做过多讲解，购买过第二本教程的用户可以复习一下 8.1. 边栏活跃用户 这一节。
    public function activedIndex(User $user)
    {
        UserResource::wrap('data');
        return UserResource::collection($user->getActiveUsers());
    }
}