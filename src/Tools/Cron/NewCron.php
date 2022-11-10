<?php

namespace LBF\Tools\Cron;

// use LBF\Tools\Cron\Server;

class NewCron {

    public Server $environment;

    public function __construct() {
        $this->environment = PHP_OS !== 'WINNT' ? Server::LINUX : Server::WIN;
    }
}

new NewCron;