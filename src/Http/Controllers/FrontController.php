<?php

namespace EmCore\Http\Controllers;

use EmCore\Models\Brand;
use Illuminate\Http\Request;
use ME\Http\Controllers\Controller;
use App\Http\Middleware\AuthorizationMiddleware;

class FrontController extends Controller
{
    public function index(Request $request)
    {
        return view('em_core::_front.index');
    }

    public function cv(Request $request)
    {
        return view('em_core::_front.cv');
    }

    public function sitemap()
    {
        return response()->view('em_core::_front.sitemap', [
            'now' => now()
        ])->header('Content-Type', 'text/xml');
    }

}
