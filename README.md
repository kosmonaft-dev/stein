# Stein PHP

## Description

Awesome DDD oriented framework, built around FrankenPHP.

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

## Contributing

1. Fork the repository.
2. Create a new branch (`git checkout -b feature/your-feature`).
3. Commit your changes (`git commit -am 'Add some feature'`).
4. Push to the branch (`git push origin feature/your-feature`).
5. Create a new Pull Request.

## License

This project is licensed under the MIT License - see the `LICENSE` file for details.