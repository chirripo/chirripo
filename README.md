Chirripo
========

Docker containers setup that can be easily added to your project by using composer.

# Instructions

- Install package: `composer require chirripo/chirripo`
- Copy env.example to root: `cp ./vendor/chirripo/chirripo/env.example .env`
- Use the package like this: `./vendor/bin/chirripo list`

# Customize

You can change any variable defined in .env to make adjustments to the provided setup. You can also create a file named `docker-compose.override.yml` in the root of your project to make more advanced customizations.