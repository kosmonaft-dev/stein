<?php

namespace Stein\Domain\Pokemon\Entity;

use Stein\Infrastructure\Pokemon\Dto\PokemonLinkDto;

class PokemonListEntity
{

    public int $count;
    public ?string $next;
    public ?string $previous;

    /** @var PokemonLinkEntity[] */
    public array $results;
}
