<?php

namespace Stein\Infrastructure\Pokemon\Dto;

class PokemonListDto
{

    public int $count;
    public ?string $next;
    public ?string $previous;

    /** @var PokemonLinkDto[] */
    public array $results;
}
