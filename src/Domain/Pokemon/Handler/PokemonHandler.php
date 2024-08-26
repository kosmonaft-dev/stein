<?php

namespace Stein\Domain\Pokemon\Handler;

use JsonMapper;
use Stein\Domain\Pokemon\Entity\{PokemonEntity, PokemonListEntity};
use Stein\Infrastructure\Pokemon\Client\PokemonClient;
use function array_map, str_replace;

class PokemonHandler
{

    public function __construct(
        protected PokemonClient $client,
        protected JsonMapper $mapper
    ) {}

    public function getPokemonList(int $offset, int $limit): PokemonListEntity
    {
        $dtos = $this->client->getPokemonList($offset, $limit);

        // Update API links to local links
        $dtos->next = $this->updateLink($dtos->next);
        $dtos->previous = $this->updateLink($dtos->previous);
        $dtos->results = array_map(function ($dto) {
            $dto->url = $this->updateLink($dto->url);
            return $dto;
        }, $dtos->results);

        return $this->mapper->map($dtos, PokemonListEntity::class);
    }

    public function getPokemonById(int $id): PokemonEntity
    {
        $dto = $this->client->getPokemonById($id);

        return $this->mapper->map($dto, PokemonEntity::class);
    }

    private function updateLink(?string $link): ?string
    {
        return $link != null ?
            str_replace('https://pokeapi.co/api/v2', env('APP_URL').'api', $link) :
            $link;
    }
}
