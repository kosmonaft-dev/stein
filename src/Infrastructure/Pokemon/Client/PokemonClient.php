<?php

namespace Stein\Infrastructure\Pokemon\Client;

use GuzzleHttp\Client;
use JsonMapper;
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
        $response = $this->client->request('GET', '/api/v2/pokemon?'.http_build_query(['offset' => $offset, 'limit' => $limit]));
        if ($response->getStatusCode() !== 200) {
            $exception = new ApiCallException('An error occurred.');
            $exception->status_code = $response->getStatusCode();
            $exception->reason_phrase = $response->getReasonPhrase();

            throw $exception;
        }

        $results = json_decode($response->getBody()->getContents());

        return $this->mapper->map($results, PokemonListDto::class);
    }

    public function getPokemonById(int $id): PokemonDto
    {
        $result = $this->call('/api/v2/pokemon/'.$id);

        return $this->mapper->map($result, PokemonDto::class);
    }

    private function call(string $uri): mixed
    {
        $response = $this->client->request('GET', $uri);
        if ($response->getStatusCode() !== 200) {
            $exception = new ApiCallException('An error occurred.');
            $exception->status_code = $response->getStatusCode();
            $exception->reason_phrase = $response->getReasonPhrase();

            throw $exception;
        }

        return json_decode($response->getBody()->getContents());
    }
}
