<?php

namespace whereof\laravel\Hprose\Servers;

use Hprose\Socket\Server;

class SocketServer extends Server
{
    /**
     * SocketServer constructor.
     *
     * @param null $uri
     */
    public function __construct($uri = null)
    {
        parent::__construct($uri);
        $this->uris = [];
        if ($uri) {
            $this->addListener($uri);
        }
    }
}