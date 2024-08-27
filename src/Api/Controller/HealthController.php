<?php

namespace Stein\Api\Controller;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;
use Stein\Framework\Attribute\{ApiController, HttpGet, Route, RouteName};

#[ApiController]
#[Route('/health', 1)]
class HealthController
{

    #[HttpGet]
    #[RouteName('healthcheck')]
    public function index(): ResponseInterface
    {
        return new Response();
    }

    #[HttpGet('/ping')]
    #[RouteName('ping')]
    public function ping(): ResponseInterface
    {
        return new TextResponse('pong');
    }
}
