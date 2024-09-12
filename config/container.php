<?php

use League\Container\{Container, ReflectionContainer};
use GuzzleHttp\Client;
use Laminas\Diactoros\ServerRequestFactory;
use Monolog\{Handler\StreamHandler, Level, Logger, Processor\PsrLogMessageProcessor};
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Stein\Framework\Application\{RequestHandler};
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
use Stein\Framework\Router\{ControllerRouteMapper, FastRouteRouter, RouterInterface};
use Psr\Log\LoggerInterface;

$container = new Container();

// Cache definitions by default
$container->defaultToShared();

// Definitions
$container->add(RouterInterface::class, function () {
    $isProduction = isProduction();

    $router = new FastRouteRouter(
        cache_file: cache_path('routes.cache.php'),
        cache_disabled: !$isProduction
    );

    $mapper = new ControllerRouteMapper(
        router: $router,
        cache_file: cache_path('controllers.cache.php'),
        cache_disabled: !$isProduction
    );

    // You can insert your controllers here for better performance
    $mapper->mapByClassString([
        \Stein\Api\Controller\IndexController::class,
        \Stein\Api\Controller\PokemonController::class,
        \Stein\Api\Controller\HealthController::class,
    ]);
    // Or load them from the file system (if you're lazy)
    // $mapper->mapByDirectory(__ROOT_DIR__.'/src/Api/Controller');

    return $router;
});

$container
    ->add(RequestHandlerInterface::class, function(ContainerInterface $container) {
        $router = $container->get(RouterInterface::class);
        $logger = $container->get(LoggerInterface::class);

        $handler = new RequestHandler();
        $handler
            ->middleware(new ErrorHandlerMiddleware($logger, isProduction()))
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
    env('LOG_CHANNEL', 'app'),
    [
        new StreamHandler('php://stderr', Level::Error, false),
        new StreamHandler('php://stdout', env('LOG_LEVEL', Level::Debug))
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
