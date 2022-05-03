<?php

namespace whereof\laravel\Hprose\Clients;

use Hprose\Socket\Client;

class SocketClient extends Client
{
    /**
     * SocketClient constructor.
     *
     * @param null $uris
     * @param bool $async
     */
    public function __construct($uris = null, $async = false)
    {
        parent::__construct($uris, $async);
    }
}