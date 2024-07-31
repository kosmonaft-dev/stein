<?php

namespace Stein\Api\Controller;

use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Stein\Framework\Attribute\{Controller, FromQuery, HttpGet, Produces, RouteName, Route};

#[Controller]
#[Route('')]
class IndexController
{

    #[HttpGet]
    #[RouteName('homepage')]
    #[Produces('text/html')]
    public function index(#[FromQuery] string $name = 'world'): ResponseInterface
    {
        return new HtmlResponse($this->sayHi($name));
    }

    #[HttpGet('/{name}')]
    #[RouteName('welcome')]
    #[Produces('text/html')]
    public function welcome(string $name): ResponseInterface
    {
        return new HtmlResponse($this->sayHi($name));
    }

    private function sayHi(string $name): string
    {
        return sprintf('Hello, %s!', $name);
    }
}
