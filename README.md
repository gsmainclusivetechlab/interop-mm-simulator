# GSMA Mobile Money API Simulator

[![Codacy grade](https://img.shields.io/codacy/grade/459e9596af7540d0af54c6f1a9ceadf5?logo=codacy)](https://www.codacy.com/gh/gsmainclusivetechlab/interop-mm-simulator?utm_source=github.com&utm_medium=referral&utm_content=gsmainclusivetechlab/interop-mm-simulator&utm_campaign=Badge_Grade)

[![CircleCI](https://img.shields.io/circleci/build/github/gsmainclusivetechlab/interop-mm-simulator/master?label=Master&logo=circleCI&token=1357bfe0731d8817433b52570534dfb986d874e6)](https://app.circleci.com/pipelines/github/gsmainclusivetechlab/interop-mm-simulator?branch=master)
[![CircleCI](https://img.shields.io/circleci/build/github/gsmainclusivetechlab/interop-mm-simulator/develop?label=Develop&logo=circleCI&token=1357bfe0731d8817433b52570534dfb986d874e6)](https://app.circleci.com/pipelines/github/gsmainclusivetechlab/interop-mm-simulator?branch=develop)

## Project Architecture

The API simulator is built using micro-services, coordinated using
`docker-compose`. Our services are:

- `mysqldb`: Provides a database for the app. Uses a lightly-customised
  off-the-shelf [mysql image](./build/Dockerfile.mysqldb). The customisation is
  just to inject our [mysql config](./build/my.cnf) into the container.
- `web`: Provides an nginx web server. Uses a lightly-customised [nginx
  image](./build/Dockerfile.web). The customisation is to add our application
  code to the container, and similarly adds [server
  config](./build/nginx-server.conf) and [SSL config](./build/ssl).
- `php`: Provides a PHP interpreter to run our application code. Uses a
  [custom image](http://github.com/gsmainclusivetechlab/interop-php-fpm),
  further [customised](./build/Dockerfile.php) to add configuration files and
  the application code. In addition, PHP dependencies are pre-installed with
  composer and artisan.
- `migrate`: A short-lived service which simply runs database migrations
  before exiting. Uses the same image as `app`, which contains
  [wait](https://github.com/ufoscout/docker-compose-wait), allowing the
  service to wait for the `mysqldb` container to be running before attempting
  the migrations.

## Project Setup

1. Clone repository
2. Navigate to your project directory
3. Copy the example environment files, and make any adjustments to reflect
   your own environment:
   - [.env.example](./.env.example) should be copied to .env
   - [src/.env.example](./src/.env.example) should be copied to src/.env

### First Run

1. Build new docker images:
   ```
   $ docker-compose build
   ```
2. Set up the database using Laravel's migration tool
   ```
   $ docker-compose run migrate bash -c "/wait && php artisan migrate:refresh --seed"
   ```
3. Launch containers using the new images:
   ```
   $ docker-compose up -d web
   ```

### Updates

After making changes to the code, you can deploy your changes by running almost the same steps as the first run. The only difference is the migration script, which should
not seed the database with initial contents:

```
# rebuild all images to include the new code
$ docker-compose build

# run the default migration script, which does not seed
$ docker-compose run migrate

# stop and destroy existing containers, then recreate using the new images
$ docker-compose up -d web
```

## Local development

When running locally, we may want our services to operate in a slightly different way.
Additional configuration files have been set up to cover two such cases:

- [`development/volumes.yml`](./development/volumes.yml): Set up shared
  volumes between your local files and the files inside the running containers,
  which allows your local changes to immediately be reflected in the running
  code.
- [`development/local-network.yml`](./development/local-network.yml): Connect
  to an existing docker network. This is useful when you also have the test
  platform running locally, as it will allow all services to communicate across
  the same docker network. Additionally, this will expose the app on your
  local machine under port 8087 (or whatever is configured as `HOST_WEB_PORT`
  in [.env](./.env.example)).

To use these configurations, select the config files when running `docker-compose up`:

```
$ docker-compose -f ./docker-compose.yml \
                 -f ./development/local-network.yml
                 -f ./development/volumes.yml
                 up web
```

### Inspecting Running Containers

Running containers should not be modified, since the changes will be lost each time
the container restarts. However, it can be useful to connect to a running container
in order to inspect the environment and debug. To do that, use the following command,
where `{service}` can be `php`, `web` or `mysqldb`:

```
$ docker-compose exec {service} bash
```

<!-- TODO: telescope

### Using telescope for check incoming requests

In `src/.env` set TELESCOPE_ENABLED=true

Run

`make php-bash`

Inside PHP container run

`php artisan telescope:install`

`php artisan migrate`

-->


## Running Tests

```
# Ensure our services are running using a local volume to share results with the host
$ docker-compose run -v "$(pwd)"/results:/tmp/results php bash 

# Run tests and test coverage inside the container
  php artisan test --log-junit /tmp/results/results.xml

  phpdbg -qrr vendor/bin/phpunit \
    --coverage-html /tmp/results/coverage-report-html \
    --coverage-clover /tmp/results/coverage-report-clover/clover.xml 
```

Note that you must use a local volume to see test results on the host machine.

TODO: Consider adding shortcut scripts for these long-winded commands