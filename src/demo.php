<?php


use whereof\laravel\Hprose\Facades\HproseRoute;

// 注册callback
HproseRoute::add(function () {
    return 'service hello';
}, 'hello');

// 注册class

HproseRoute::add(\whereof\laravel\Hprose\DemoService::class);

//注册中间价
HproseRoute::addInvokeHandler(function ($name, array &$args, stdClass $context, Closure $next) {
    $result = $next($name, $args, $context);
    return $result;
});

// 注册整个目录
HproseRoute::addPath(app_path('Services'));
