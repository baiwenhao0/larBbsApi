<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
//Route::prefix('v1')->name('api.v1.')->group(function() {
//    Route::get('version', function() {
//        abort(403, 'test');
//        return 'this is version v1';
//    })->name('version');
//});
Route::prefix('v1')
    ->namespace('Api')
    ->middleware('change-locale')
    ->name('api.v1.')
//                ->middleware('throttle:1,1')
    ->group(function () {
    Route::middleware('throttle:' . config('api.rate_limits.sign'))
        ->group(function () {
            // 图片验证码
            Route::post('captchas', 'CaptchasController@store')
                ->name('captchas.store');
            // 短信验证码
            Route::post('verificationCodes', 'VerificationCodesController@store')
                ->name('verificationCodes.store');
            // 用户注册
            Route::post('users', 'UsersController@store')
                ->name('users.store');
            // 第三方登录注意这里的参数，我们对 social_type 进行了限制，只会匹配 weixin，如果你增加了其他的第三方登录，
            //可以再这里增加限制，例如支持微信及微博：->where('social_type', 'weixin|weibo') 。
            Route::post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')
                ->where('social_type', 'weixin')
                ->name('socials.authorizations.store');
            // 登录
            Route::post('authorizations', 'AuthorizationsController@store')
                ->name('api.authorizations.store');
        });

    Route::middleware('throttle:' . config('api.rate_limits.access'))
        ->group(function () {
            // 游客可以访问的接口

            // 话题列表，详情
            Route::resource('topics', 'TopicsController')->only([
                'index', 'show'
            ]);
            // 某个用户的详情
            Route::get('users/{user}', 'UsersController@show')
                ->name('users.show');

            // 登录后可以访问的接口
            Route::middleware('auth:api')->group(function() {
                // 当前登录用户信息
                Route::get('user', 'UsersController@me')
                    ->name('user.show');
                // 分类列表
                Route::get('categories', 'CategoriesController@index')
                    ->name('categories.index');
                // 某个用户发布的话题
                Route::get('users/{user}/topics', 'TopicsController@userIndex')
                    ->name('users.topics.index');
                // 发布话题
                Route::resource('topics', 'TopicsController')->only([
                    'store', 'update', 'destroy'
                ]);
                // 编辑登录用户信息
                Route::patch('user', 'UsersController@update')
                    ->name('user.update');
                // 上传图片
                Route::post('images', 'ImagesController@store')
                    ->name('images.store');

                // 标记消息通知为已读
                Route::patch('user/read/notifications', 'NotificationsController@read')
                    ->name('user.notifications.read');
                // 当前登录用户权限
                Route::get('user/permissions', 'PermissionsController@index')
                    ->name('user.permissions.index');

                // 某个用户的回复列表
                Route::get('users/{user}/replies', 'RepliesController@userIndex')
                    ->name('users.replies.index');
                // 资源推荐
                Route::get('links', 'LinksController@index')
                    ->name('links.index');

                // 活跃用户
                Route::get('actived/users', 'UsersController@activedIndex')
                    ->name('actived.users.index');
            });
        });

    // 刷新token
    Route::put('authorizations/current', 'AuthorizationsController@update')
        ->name('authorizations.update');
    // 删除token
    Route::delete('authorizations/current', 'AuthorizationsController@destroy')
        ->name('authorizations.destroy');





    // 短信验证码
//    Route::post('verificationCodes', 'VerificationCodesController@store')
//        ->name('verificationCodes.store');
    // 用户注册
//    Route::post('users', 'UsersController@store')
//        ->name('users.store');



});