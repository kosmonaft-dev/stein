<?php

namespace Stein\Infrastructure\Pokemon\Client;

use GuzzleHttp\Client;
use JsonMapper;
use Psr\Http\Message\ResponseInterface;
use Stein\Framework\Http\Error\ApiCallException;
use Stein\Infrastructure\Pokemon\Dto\{PokemonDto, PokemonListDto};
use function json_decode;

class PokemonClient
{

    public function __construct(
        protected Client $client,
        protected JsonMapper $mapper
    ) {}

    public function getPokemonList(int $offset, int $limit): PokemonListDto
    {
        $response = $this->callApi('/api/v2/pokemon?'.http_build_query(['offset' => $offset, 'limit' => $limit]));
        $results = json_decode($response->getBody()->getContents());

        return $this->mapper->map($results, PokemonListDto::class);
    }

    public function getPokemonById(int $id): PokemonDto
    {
        $response = $this->callApi('/api/v2/pokemon/'.$id);
        $result = json_decode($response->getBody()->getContents());

        return $this->mapper->map($result, PokemonDto::class);
    }

    private function callApi(string $uri): ResponseInterface
    {
        $response = $this->client->request('GET', $uri);
        if ($response->getStatusCode() !== 200) {
            $exception = new ApiCallException('An error occurred with the API call to '.$uri);
            $exception->status_code = $response->getStatusCode();
            $exception->reason_phrase = $response->getReasonPhrase();

            throw $exception;
        }

        return $response;
    }
}
