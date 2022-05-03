<?php

namespace whereof\laravel\Hprose;

use Illuminate\Support\ServiceProvider;
use whereof\laravel\Hprose\Clients\SocketClient;
use whereof\laravel\Hprose\Commands\SocketCommand;
use whereof\laravel\Hprose\Routing\Router;
use whereof\laravel\Hprose\Servers\SocketServer;
use stdClass;
use Illuminate\Support\Facades\Route;

class HproseServiceProvider extends ServiceProvider
{

    public function boot()
    {

        $configSource = realpath(__DIR__ . '/../config/config.php');
        if ($this->app instanceof \Illuminate\Foundation\Application) {
            $this->publishes([$configSource => config_path('hprose.php')]);
            $demoRoute = realpath(__DIR__ . '/demo.php');
            $this->publishes([$demoRoute => base_path('rpc/demo.php')]);

        }
        if ($this->app instanceof \Laravel\Lumen\Application) {
            $this->app->configure('hprose');
        }
        $this->mergeConfigFrom($configSource, 'hprose');
        if ($files = (array)config('hprose.server.route_path', glob(base_path('rpc') . '/*.php'))) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    require $file;
                }
            }
        }
        $this->publishes([realpath(__DIR__ . '/daemon.sh') => base_path('hprose.sh')]);

        if (config('hprose.server.http.enable', false)) {
            $this->app->router->group([
                'prefix'    => config('hprose.server.http.route_prefix', 'hprose'),
                'namespace' => '\\PhpScript\\LaravelHprose\\Http\\Controller'
            ], function ($router) {
                $router->get('/', 'HproseController@index');
            });
        }
    }

    /**
     * 注册.
     */
    public function register()
    {
        $this->app->singleton('hprose.router', function ($app) {
            return new Router();
        });

        $this->app->singleton('hprose.socket.client', function ($app) {
            $uris            = config('hprose.client.tcp_uris');
            $async           = config('hprose.client.async', false);
            $client          = new SocketClient($uris, $async);
            $client->onError = function ($name, $error) {

            };
            return $client;
        });

        $this->app->singleton('hprose.socket.server', function ($app) {
            $service              = new SocketServer();
            $service->onSendError = function ($error, stdClass $context) {

            };
            $uris                 = config('hprose.server.tcp_uris');
            array_map(function ($url) use (&$service) {
                $service->addListener($url);
            }, $uris);
            //用来设置服务器是否是工作在 debug 模式下，
            //在该模式下，当服务器端发生异常时，将会将详细的错误堆栈信息返回给客户端，否则，只返回错误信息。
            $service->debug = config('hprose.server.debug', false);
            //null、数字（包括整数、浮点数）、Boolean 值、字符串、日期时间等基本类型的数据或者不包含引用的数组和对象。
            //当该属性设置为 true 时，在进行序列化操作时，将忽略引用处理，加快序列化速度.
            //将该属性设置为 true，可能会因为死循环导致堆栈溢出的错误。
            $service->simple = config('hprose.server.simple', false);
            //表示在调用执行时，如果发生异常，将延时一段时间后再返回给客户端。
            $service->errorDelay = config('hprose.server.errorDelay', 10000);
            return $service;
        });
        $this->commands([
            SocketCommand::class,
        ]);
    }
}
