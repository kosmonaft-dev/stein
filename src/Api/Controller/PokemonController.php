<?php

namespace Stein\Api\Controller;

use JsonMapper;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Stein\Api\Controller\ViewModel\{PokemonListViewModel, PokemonViewModel};
use Stein\Domain\Pokemon\Handler\PokemonHandler;
use Stein\Framework\Attribute\{ApiController, FromQuery, HttpGet, Produces, ProducesResponseType, Route, RouteName};
use Stein\Framework\Http\Error\ProblemDetails;

#[ApiController]
#[Route('/api/pokemon', 2)]
class PokemonController
{

    public function __construct(
        protected PokemonHandler $handler,
        protected JsonMapper $mapper
    ) {}

    #[HttpGet]
    #[RouteName('getPokemonList')]
    #[Produces('application/json')]
    #[ProducesResponseType(PokemonListViewModel::class, 200)]
    #[ProducesResponseType(ProblemDetails::class, 500)]
    public function getPokemonList(#[FromQuery] int $offset = 0, #[FromQuery] int $limit = 20): ResponseInterface
    {
        $pokemon_list = $this->handler->getPokemonList($offset, $limit);
        $view_model = $this->mapper->map($pokemon_list, PokemonListViewModel::class);

        return new JsonResponse($view_model);
    }

    #[HttpGet('/{id:\d+}')]
    #[RouteName('getPokemonById')]
    #[Produces('application/json')]
    #[ProducesResponseType(PokemonViewModel::class, 200)]
    #[ProducesResponseType(ProblemDetails::class, 404)]
    #[ProducesResponseType(ProblemDetails::class, 500)]
    public function getPokemonById(int $id): ResponseInterface
    {
        $pokemon = $this->handler->getPokemonById($id);
        $view_model = $this->mapper->map($pokemon, PokemonViewModel::class);

        return new JsonResponse($view_model);
    }
}
