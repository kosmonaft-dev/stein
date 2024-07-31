<?php
/*
 * This script was made to be used in the "worker mode" of FrankenPHP.
 * For more details, see the documentation : https://frankenphp.dev/docs/worker/ .
 * ```bash
 * docker run -e FRANKENPHP_CONFIG="worker ./public/worker.php" -v $PWD:/app -p 80:8080 -p 443:443 -p 443:443/udp --tty dunglas/frankenphp
 * ```
 */

ignore_user_abort(true);

require_once __DIR__ . '/../vendor/autoload.php';

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Stein\Framework\Application\WebApplication;

// Warning, see: https://www.php.net/manual/en/timezones.others.php
// do not use any of the timezones listed here (besides UTC)
date_default_timezone_set(env('TIMEZONE', 'UTC'));

/** @var ContainerInterface $container */
$container = (require_once __DIR__ . '/../config/container.php');

$app = $container->get(WebApplication::class);

$handler = static function () use ($app, $container) {
    $request = $container->get(ServerRequestInterface::class);
    $app->run($request);
};

$max_requests_number = env('FRANKEN_MAX_REQUESTS', __FRANKEN_DEFAULT_MAX_REQUESTS__);
for ($current_requests_number = 0, $running = true; $current_requests_number < $max_requests_number && $running; ++$current_requests_number) {
    $running = \frankenphp_handle_request($handler);

    gc_collect_cycles();
}
