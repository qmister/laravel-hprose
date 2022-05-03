<?php

return [
    //rpc 服务
    'server' => [
        // hprose 调试模式
        'debug'      => true,
        //监听地址
        'tcp_uris'   => [
            'tcp://0.0.0.0:1314',
        ],
        //注册rpc 服务 目录地址
        'route_path' => glob(base_path('rpc').'/*.php'),
        // 通过路由查看注册的方法
        'http'       => [
            // 如果设置false 在控制台显示调用方法，否在在路由显示调用方法
            'enable'       => true,
            //如果设置了true 这里就是路由前缀
            'route_prefix' => 'rpc',
        ],
    ],
    //rpc 客户端
    'client' => [
        // 服务端监听地址
        'tcp_uris' => [
            'tcp://127.0.0.1:1314',
        ],
        //是否异步
        'async'    => false,
    ],
];