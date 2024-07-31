<?php

use League\Container\{Container, ReflectionContainer};
use GuzzleHttp\Client;
use Laminas\Diactoros\ServerRequestFactory;
use Monolog\{Handler\StreamHandler, Level, Logger, Processor\PsrLogMessageProcessor};
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Stein\Framework\Application\{ControllerLoader, RequestHandler};
use Stein\Framework\Http\Middleware\{BodyParserMiddleware,
    ContentLengthMiddleware,
    DispatchMiddleware,
    ErrorHandlerMiddleware,
    ImplicitHeadMiddleware,
    ImplicitOptionsMiddleware,
    MethodNotAllowedMiddleware,
    NotFoundMiddleware,
    RouteMiddleware,
    TrailingSlashMiddleware,
    UploadedFilesParserMiddleware};
use Stein\Framework\Router\{FastRouteRouter, RouterInterface};
use Psr\Log\LoggerInterface;

$container = new Container();

// Cache definitions by default
$container->defaultToShared();

// Definitions
$container->add(RouterInterface::class, function () {
    $isProduction = env('APP_ENV') == 'production';

    $router = new FastRouteRouter(
        cache_file: cache_path('routes.cache.php'),
        cache_disabled: !$isProduction
    );

    if ($isProduction && file_exists(cache_path('routes.cache.php'))) {
        return $router;
    }

    $controller_loader = new ControllerLoader([__ROOT_DIR__.'/src/Api/Controller'], $router);
    $controller_loader->loadRoutes();

    return $router;
});

$container
    ->add(RequestHandlerInterface::class, function(ContainerInterface $container) {
        $router = $container->get(RouterInterface::class);
        $logger = $container->get(LoggerInterface::class);

        $handler = new RequestHandler();
        $handler
            ->middleware(new ErrorHandlerMiddleware($logger))
            ->middleware(new TrailingSlashMiddleware())
            ->middleware(new ContentLengthMiddleware())
            ->middleware(new RouteMiddleware($router, $container))
            ->middleware(new ImplicitHeadMiddleware($router))
            ->middleware(new ImplicitOptionsMiddleware())
            ->middleware(new MethodNotAllowedMiddleware())
            ->middleware(new BodyParserMiddleware())
            ->middleware(new UploadedFilesParserMiddleware())
            ->middleware(new DispatchMiddleware())
            ->middleware(new NotFoundMiddleware());

        return $handler;
    })
    ->addArgument($container);

$container->add(ServerRequestInterface::class, fn() => ServerRequestFactory::fromGlobals());

$container->add(LoggerInterface::class, fn() => new Logger(
    env('LOG_CHANNEL'),
    [
        new StreamHandler(
            'php://stdout',
            env('LOG_LEVEL', Level::Debug)
        )
    ],
    [
        new PsrLogMessageProcessor(removeUsedContextFields: true)
    ],
    new DateTimeZone(env('TIMEZONE', 'UTC'))
));

$container->add(Client::class, fn() => new Client(['base_uri' => 'https://pokeapi.co', 'http_errors' => false]));

// Auto-wiring
$container->delegate(new ReflectionContainer(true));

return $container;
