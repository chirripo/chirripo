Chirripo
========

[![Build Status](https://travis-ci.org/chirripo/chirripo.svg?branch=master)](https://travis-ci.org/chirripo/chirripo)

Docker containers setup that can be easily added to your project by using composer.

# Instructions

- Install package: `composer require --dev chirripo/chirripo`
- Copy env.example to root: `cp ./vendor/chirripo/chirripo/env.example .env`
- Use the package like this: `./vendor/bin/chirripo list`

# Customize

You can change any variable defined in .env to make adjustments to the provided setup. You can also create a file named `docker-compose.override.yml` in the root of your project to make more advanced customizations.

# Xdebug

- In order to setup xdebug, set XDEBUG_ENABLE variable to "enable", then stop & start the containers.

# Commands

For drush, ssh and compose commands; if you need to forward options to the actual command, you need to use --

Examples:
- chirripo compose -- logs -f nginx
- chirripo drush -- --version
