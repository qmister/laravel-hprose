## 安装

~~~
composer require whereof/laravel-hprose
~~~

## 配置文件

~~~
<?php

return [
    //rpc 服务
    'server' => [
        // hprose 调试模式
        'debug' => true,
        //监听地址
        'tcp_uris'       => [
            'tcp://0.0.0.0:1314',
        ],
        //注册rpc 服务 目录地址
        'route_path'     => glob(base_path("rpc") . '/*.php'),
        // 通过路由查看注册的方法
        'http'           => [
            // 如果设置false 在控制台显示调用方法，否在在路由显示调用方法
            'enable'       => false,
            //如果设置了true 这里就是路由前缀
            'route_prefix' => 'rpc'
        ],
    ],
    //rpc 客户端
    'client' => [
        // 服务端监听地址
        'tcp_uris' => [
            'tcp://127.0.0.1:1314',
        ],
        //是否异步
        'async'    => false
    ],
];
~~~


## Laravel配置

~~~
//在 `config/app.php` 注册 HproseServiceProvider 
'providers' => [
    .....
    \whereof\laravel\Hprose\HproseServiceProvider::class
]
php artisan vendor:publish --provider="whereof\laravel\Hprose\HproseServiceProvider"
~~~

## Lumen配置

~~~
将配置信息放在/config/hprose.php
/bootstrap/app.php
$app->register(\whereof\laravel\Hprose\HproseServiceProvider::class);
$app->withFacades();
~~~

## 服务端 方法注入，类注入以及目录下类注入 `rpc/demo.php`

~~~
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
~~~

## 启动rpc服务

~~~
php artisan hprose:socket
~~~

## 客户端调用

~~~
$uris   = ['tcp://127.0.0.1:1314'];
$client = new \whereof\laravel\Hprose\Clients\SocketClient($uris, false);
$client->hello();

app('hprose.socket.client')->whereof_laravel_hprose_demoservice->index()

app('hprose.socket.client')->whereof->laravel->hprose->demoservice->index()
~~~

## 加入我们

如果你认可我们的开源项目，有兴趣为 laravel-hprose 的发展做贡献，竭诚欢迎加入我们一起开发完善。无论是[报告错误](https://github.com/tp5er/laravel-hprose/issues)或是
[Pull Request](https://github.com/tp5er/laravel-hprose/pulls) 开发，那怕是修改一个错别字也是对我们莫大的帮助。
