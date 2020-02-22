<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Http\Controllers\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    //我们在 Api/Controller 中加了 errorResponse 方法，所以我们在任意 API 控制器中直接使用 $this->errorResponse 即可。
    public function errorResponse($statusCode, $message=null, $code=0)
    {
        throw new HttpException($statusCode, $message, null, [], $code);
    }
}
