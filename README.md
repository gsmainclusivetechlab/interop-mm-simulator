GSMA Mobile Money API Simulator
===================================

[![Codacy grade](https://img.shields.io/codacy/grade/459e9596af7540d0af54c6f1a9ceadf5?logo=codacy)](https://www.codacy.com/gh/gsmainclusivetechlab/interop-mm-simulator?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=gsmainclusivetechlab/interop-mm-simulator&amp;utm_campaign=Badge_Grade)

[![CircleCI](https://img.shields.io/circleci/build/github/gsmainclusivetechlab/interop-mm-simulator/master?label=Master&logo=circleCI&token=1357bfe0731d8817433b52570534dfb986d874e6)](https://app.circleci.com/pipelines/github/gsmainclusivetechlab/interop-mm-simulator?branch=master)
[![CircleCI](https://img.shields.io/circleci/build/github/gsmainclusivetechlab/interop-mm-simulator/develop?label=Develop&logo=circleCI&token=1357bfe0731d8817433b52570534dfb986d874e6)](https://app.circleci.com/pipelines/github/gsmainclusivetechlab/interop-mm-simulator?branch=develop)

BASE STRUCTURE
-------------------
```folder_structure
  build/		contains docker container config
  runtime/
    |-- bash/		Bash history (composer cache, bash commands history)
    |-- mysql/		MySQL databases for docker container
  src/			Laravel application code (Project)
```

INSTALLATION
------------

Project can be setup with Docker.

1.  Clone repository
2.  Navigate to your project directory
3.  Run `make init`, this command will copy important files from examples:
    -  .env
    -  docker-compose.yml
    -  src/.env
    -  build/nginx-server.conf
4.  Check .env files for correct configurations.
5.  Run a test run to build containers and init DB: `make test-run`. After containers up press "Ctrl+C" to exit.
6.  Run containers with `make run`
7.  Add to your `/etc/hosts` file: `127.0.0.1 itp-mmo-api.test`
8.  Run installation `make install`  

### Docker PHP Container

To get inside PHP container to run composer/php commands run this command:

`make php-bash`

Inside PHP container there is also GNU Make utility, run `make` without any parameters to get available commands list.

### Prettier usage

To run prettier install nodejs on your machine and run:

`npm run prettier`

### Using telescope for check incoming requests

In `src/.env` set TELESCOPE_ENABLED=true

Run

`make php-bash`

Inside PHP container run

`php artisan telescope:install`

`php artisan migrate`

### Site access

Access your site via URL: <http://itp-mmo-api.test:8084>

You're ready to write your code
------------
