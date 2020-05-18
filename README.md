GSMA Mobile Money API Simulator
===================================

[![CircleCI](https://img.shields.io/circleci/build/github/gsmainclusivetechlab/interop-mm-simulator/master?label=Master&logo=circleCI&token=1357bfe0731d8817433b52570534dfb986d874e6)](https://app.circleci.com/pipelines/github/gsmainclusivetechlab/interop-mm-simulator?branch=master)
[![CircleCI](https://img.shields.io/circleci/build/github/gsmainclusivetechlab/interop-mm-simulator/develop?label=Develop&logo=circleCI&token=1357bfe0731d8817433b52570534dfb986d874e6)](https://app.circleci.com/pipelines/github/gsmainclusivetechlab/interop-mm-simulator?branch=develop)

BASE STRUCTURE
-------------------
```
  build/		contains docker container config
  runtime/
    |-- bash/		Bash history (composer cache, bash commands history)
    |-- mysql/		MySQL databases for docker container
  src/			Laravel application code (Project)
```

INSTALLATION
------------

Project can be setup with Docker.

(If you have a Mac machine - we recommend to run docker under Vagrant. See [Vagranfile and Instructions](https://bitbucket.org/snippets/justcoded/Aex4nL/))

1. Clone repository
2. Navigate to your project directory
3. Run `make init`, this command will copy important files from examples:
    - .env
    - docker-compose.yml
    - src/.env
    - build/nginx-server.conf
4. Check .env files for correct configurations.
5. Login to JC docker hub: `docker login hub.jcdev.net:24000`
6. Run a test run to build containers and init DB: `make test-run`. After containers up press "Ctrl+C" to exit.
7. Run containers with `make run`
7. Add to your `/etc/hosts` file: `127.0.0.1 itp-mmo-api.test`
9. Run installation `make install`  

### Docker PHP Container

To get inside PHP container to run composer/php commands run this command:

`make php-bash`

Inside PHP container there is also GNU Make utility, run `make` without any parameters to get available commands list.

### Using telescope for check incoming requests

In `src/.env` set TELESCOPE_ENABLED=true

Run

`make php-bash`

Inside PHP container run

`php artisan telescope:install`

`php artisan migrate`

### Site access:

Access your site via URL: http://itp-mmo-api.test:8084

Mail catcher: http://itp-mmo-api.test:8086

You're ready to write your code!
------------
