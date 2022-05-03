<?php

namespace whereof\laravel\Hprose\Http\Controller;

use whereof\laravel\Hprose\Facades\HproseRoute;

if (class_exists(\Illuminate\Routing\Controller::class)) {
    class BaseController extends \Illuminate\Routing\Controller
    {
    }
} elseif (class_exists(\Laravel\Lumen\Routing\Controller::class)) {
    class BaseController extends \Laravel\Lumen\Routing\Controller
    {
    }
}

class HproseController extends BaseController
{
    public function __construct()
    {
        app('view')->getFinder()->prependLocation(__DIR__ . '/../Views/');
    }

    /**
     * @return Factory|View
     */
    public function index()
    {

        return view('index', ['routers' => HproseRoute::getRouters()]);
    }
}
