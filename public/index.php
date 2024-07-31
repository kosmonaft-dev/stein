<?php

require_once __DIR__.'/../vendor/autoload.php';

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Stein\Framework\Application\WebApplication;

(static function () {
    // Warning, see: https://www.php.net/manual/en/timezones.others.php
    // do not use any of the timezones listed here (besides UTC)
    date_default_timezone_set(env('TIMEZONE', 'UTC'));

    /** @var ContainerInterface $container */
    $container = (require_once __DIR__.'/../config/container.php');

    $app = $container->get(WebApplication::class);
    $request = $container->get(ServerRequestInterface::class);

    $app->run($request);
})();
