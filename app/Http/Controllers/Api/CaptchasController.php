<?php

namespace App\Http\Controllers\Api;

//use App\Http\Controllers\Controller;
//use Illuminate\Http\Request;
use Mews\Captcha\Captcha;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
//use Gregwar\Captcha\CaptchaBuilder;
use App\Http\Requests\Api\CaptchaRequest;
class CaptchasController extends Controller
{
    //Mews\Captcha\Captcha  手机验证
    public function store(CaptchaRequest $request, Captcha $captcha)
    {
//        dd('kk');
        //扩展板自动验证
//        $this->validate($request, [
//            'captcha' => 'required|captcha'
//        ]);
        $captchaInfo = $captcha->create('flat', true);
//        dd($captchaInfo);
        return response()->json($captchaInfo)->setStatusCode(201);
    }
    //教程版本的手机验证
    public function stores(CaptchaRequest $request, CaptchaBuilder $captchaBuilder)
    {
        $key = 'captcha-'.Str::random(15);
        $phone = $request->phone;

        $captcha = $captchaBuilder->build();
        $expiredAt = now()->addMinutes(2);
        \Cache::put($key, ['phone' => $phone, 'code' => $captcha->getPhrase()], $expiredAt);

        $result = [
            'captcha_key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
            'captcha_image_content' => $captcha->inline()
        ];

        return response()->json($result)->setStatusCode(201);
    }

}
