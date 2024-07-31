<?php

namespace Stein\Api\Controller\ViewModel;

class PokemonListViewModel
{

    public int $count;
    public ?string $next;
    public ?string $previous;

    /** @var PokemonLinkViewModel[] */
    public array $results;
}
