<?php

namespace App\Http\Controllers\Api;

use Mews\Captcha\Captcha;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Overtrue\EasySms\EasySms;
use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Auth\AuthenticationException;
class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request, EasySms $easySms,Captcha $captcha)
    {
        if(!captcha_api_check($request->captcha_code, $request->key)) {
            \Cache::forget($request->captcha_key); //清除验证码缓存
//            $message = $exception->getException('aliyun')->getMessage();
            abort(500, '验证码错误' ?: '短信发送异常');
//            $this->response->errorUnauthorized('验证码错误');
        }
        $phone = $request->phone;

        // 生成4位随机数，左侧补0
        $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

        try {
            $result = $easySms->send($phone, [
                'template' => config('easysms.gateways.aliyun.templates.register'),
                'data' => [
                    'code' => $code
                ],
            ]);
        } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
            $message = $exception->getException('aliyun')->getMessage();
            abort(500, $message ?: '短信发送异常');
        }

        $key = 'verificationCode_'.Str::random(15);
        $expiredAt = now()->addMinutes(5);
        // 缓存验证码 5 分钟过期。
        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        return response()->json([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
    //带图片验证的手机验证码
    public function stores(VerificationCodeRequest $request, EasySms $easySms,Captcha $captcha)
    {
//        dd('jj');
//        $captchaData = \Cache::get($request->captcha_key);
//        if (!$captchaData) {
//            abort(403, '图片验证码已失效');
//        }
        //扩展板自动验证
//        $this->validate($request, [
//            'captcha' => 'required|captcha'
//        ]);
//        dd('dddd'.$request->captcha_code);//输入的验证码
//        dd('dddd'.$request->key);//生成的验证码密文
        if(!captcha_api_check($request->captcha_code, $request->key)) {
            \Cache::forget($request->captcha_key);
            $this->response->errorUnauthorized('验证码错误');
        }
//        \Cache::forget($request->captcha_key); //清除验证缓存
        $key = 'verificationCode_'.Str::random(15);
        $expiredAt = now()->addMinutes(5);
        $code = '1234';
        // 缓存验证码 5 分钟过期。
        \Cache::put($key, ['phone' => $request->phone, 'code' => $code], $expiredAt);
        return response()->json([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);

        $phone = $request->phone;
        if (!app()->environment('production')) {
            $code = '1234';
        } else {
            // 生成4位随机数，左侧补0
            $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);
            try {
                $result = $easySms->send($phone, [
                    'template' => config('easysms.gateways.aliyun.templates.register'),
                    'data' => [
                        'code' => $code
                    ],
                ]);
            } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
                $message = $exception->getException('aliyun')->getMessage();
                abort(500, $message ?: '短信发送异常');
            }
        }
        $key = 'verificationCode_'.Str::random(15);
        $expiredAt = now()->addMinutes(5);
        // 缓存验证码 5 分钟过期。
        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        return response()->json([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
}