## Project setup with `docker` & `compose`

This guide provides detailed instructions on setting up this project using the [`shinsenter/laravel`](https://hub.docker.com/r/shinsenter/php) docker image. This setup simplifies the development process by providing an isolated environment with all necessary dependencies.

### Prerequisites

Before starting, ensure you have the following installed on your local machine:

- Docker: [Installation Guide](https://docs.docker.com/get-docker/)
- Docker Compose: [Installation Guide](https://docs.docker.com/compose/install/)

### Getting Started

#### Clone the Repository

If you have an existing Laravel project, clone it to your local machine. If you need to create a new project, follow the steps below.

```sh
git clone git@github.com:whilesmart/trakli-webservice.git
cd trakli-webservice
```

#### Configure Laravel Environment

Create a `.env` file in the root directory of your project, if it doesn't exist already. Adjust the database settings to match the environment variables specified in the `docker-compose.yml` file.

```sh
cp .env.example .env
```

#### Start Docker Containers

Run the following command to start your Docker containers:

```sh
docker compose up -d
```

#### Install composer dependencies

Once the containers are up and running, you need to install the composer dependencies. You can do this by executing a command inside the `app` container.
NOTE: If you set up your ssh key with a passphrase, you will be prompted to enter it when running the command below.

```sh
docker compose exec app composer install
```

#### Generate Application Key

Once the containers are up and running, you need to generate an application key for Laravel. You can do this by executing a command inside the `app` container.

```sh
docker compose exec app php artisan key:generate
```

#### Run Database Migrations

To set up your database, run the migrations and seeds using the following command:

```sh
docker compose exec app php artisan migrate --seed
```

#### Access the Application

Your Laravel application should now be running and accessible at `http://localhost:8000`.
PHPMyAdmin is also available at `http://localhost:8080`.
Mailhog is available at `http://localhost:8025`.

## Additional Commands

### Accessing the Container

To open a shell session inside the `app` container:

```sh
docker-compose exec app bash
```

### Stopping the Containers

To stop the running containers:

```sh
docker-compose down
```
### Bare metal

- `git clone git@github.com:whilesmart/trakli-webservice.git`
- `cd mymo`
- `composer install`
- `cp .env.example .env`
- `php artisan key:generate`
- `php artisan migrate --seed`
- `php artisan storage:link` [Create a symlink for `storage/app/public` files to be accessible in the `public` directory]

## Built on Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Laravel Docs](https://laravel.com/docs/).
