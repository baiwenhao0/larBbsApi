<?php

namespace App\Http\Controllers\Api;

use App\Models\Link;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\LinkResource;
class LinksController extends Controller
{
    //
    public function index(Link $link)
    {
        $links = $link->getAllCached();

        return LinkResource::collection($links);
    }
}
