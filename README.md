# Stein PHP

## Description

Awesome DDD oriented framework, built around FrankenPHP.  

It is best suited for microservices and small to medium-sized applications, but can be used for larger applications as well.

## Getting Started

1. Create a new project using Composer:

```sh
composer create-project stein/stein [your-app-name]
```

2. Set up your environment variables by copying the `.env` file (if not already done):

```sh
cp .env.example .env
```

3. Modify the `.env` file to suit your environment.

4. Run the application:

You'll need docker and docker-compose installed on your machine.  
A container running `dunglas/frankenphp` container will be started and the application will be served on `https://localhost/`.

```sh
composer serve
```

## Configuration

- The `.env` file contains configuration settings for your application.
- Key settings include `APP_ENV`, `APP_DEBUG`, `APP_URL`, and `APP_KEY`.

## Directory Structure

- `bootstrap/`: Contains the application bootstrap files.
- `config/`: Configuration files for the application.
- `src/`: Source code for the application.
- `public/`: Publicly accessible files, including the entry point (`index.php`).
- `storage/`: Storage for logs, cache, and other generated files.

## Route Definition Using Attributes

Routes in this framework are defined using PHP attributes.  
This allows you to annotate your controller methods with route information directly and keep everything in one place.

It is also possible to give more instruction to a controller method via attributes, such as the HTTP methods it should
respond to or if it should fetch a parameter from the request body or query parameter.

### Example

```php
namespace Stein\Api\Controller;

use ...;

#[ApiController] // This attribute tells the framework that this class is a controller.
#[Route('/api/pokemon')] // This attribute tells the framework that this controller is responsible for the /api/pokemon base route.
class PokemonController
{

    public function __construct(
        protected PokemonHandler $handler,
        protected JsonMapper $mapper
    ) {}

    #[HttpGet] // This attribute tells the framework that this method should respond to GET requests.
    #[RouteName('getPokemonList')] // This attribute gives a name to the route.
    #[Produces('application/json')] // This attribute tells the framework that this method produces JSON responses.
    #[ProducesResponseType(PokemonListViewModel::class, 200)] // This attribute tells the framework that this method can return a PokemonListViewModel on success.
    #[ProducesResponseType(ProblemDetails::class, 500)] // This attribute tells the framework that this method can return a ProblemDetails on error.
    public function getPokemonList(#[FromQuery] int $offset = 0, #[FromQuery] int $limit = 20): ResponseInterface
    {
        // $offset and $limit are fetched from the query parameters of the request.

        $pokemon_list = $this->handler->getPokemonList($offset, $limit);
        $view_model = $this->mapper->map($pokemon_list, PokemonListViewModel::class);

        return new JsonResponse($view_model);
    }

    #[HttpGet('/{id:\d+}')] // This attribute tells the framework that this method should respond to GET requests with a numeric id.
                            // Therefore, the URL that reaches this method should be /api/pokemon/{id}.
    #[RouteName('getPokemonById')]
    #[Produces('application/json')]
    #[ProducesResponseType(PokemonViewModel::class, 200)]
    #[ProducesResponseType(ProblemDetails::class, 404)]
    #[ProducesResponseType(ProblemDetails::class, 500)]
    public function getPokemonById(string $id): ResponseInterface
    {
        $pokemon = $this->handler->getPokemonById($id);
        $view_model = $this->mapper->map($pokemon, PokemonViewModel::class);

        return new JsonResponse($view_model);
    }
}
```

## Contributing

1. Fork the repository.
2. Create a new branch (`git checkout -b feature/your-feature`).
3. Commit your changes (`git commit -am 'Add some feature'`).
4. Push to the branch (`git push origin feature/your-feature`).
5. Create a new Pull Request.

## License

This project is licensed under the MIT License - see the `LICENSE` file for details.