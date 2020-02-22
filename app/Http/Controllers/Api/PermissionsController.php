<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\PermissionResource;
class PermissionsController extends Controller
{
    //
    public function index(Request $request)
    {
        $permissions = $request->user()->getAllPermissions();

        PermissionResource::wrap('data');
        return PermissionResource::collection($permissions);
    }
}
