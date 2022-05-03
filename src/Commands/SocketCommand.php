<?php

namespace whereof\laravel\Hprose\Commands;

use Illuminate\Console\Command;
use whereof\laravel\Hprose\Facades\HproseRoute;


class SocketCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'hprose:socket';
    /**
     * @var string
     */
    protected $description = 'hprose rpc socket';

    public function handle()
    {
        $this->hrposeLog();
        app('hprose.socket.server')->start();
    }

    public function hrposeLog()
    {
        if (app() instanceof \Illuminate\Foundation\Application) {
            $this->output->writeln('laravel (' . app()->version() . ')');
        }
        if (app() instanceof \Laravel\Lumen\Application) {
            $this->output->writeln(app()->version());
        }
        $this->output->newLine();
        $uris = config('hprose.server.tcp_uris');
        foreach ($uris as $uri) {
            $this->line(sprintf(' - <info>%s</>', $uri));
        }
        $this->comment('可调用远程方法:');
        $this->output->newLine();
        if ($routers = HproseRoute::getRouters()) {
            foreach ($routers as $arg) {
                $this->line(sprintf(' - <info>%s</>', $arg['methods']));
            }
            $this->output->newLine();
        } else {
            $this->line(sprintf(' - <info>无可调用方法</>'));
        }
    }
}
