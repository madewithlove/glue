# Glue
[![Build Status](https://travis-ci.org/madewithlove/nanoframework-helpers.svg)](https://travis-ci.org/madewithlove/nanoframework-helpers)

Glue is a nano-framework made to quickly bootstrap packages-based applications.
At its core it's just a container and a quick PSR7 setup, on top of which are glued together service providers and middlewares. So although the defaults leverage `league/route` and `twig/twig` per example, anything can be used with it.

Default providers include:
- A base routing system using `league/route`
- A PSR7 stack using `zendframework/zend-diactoros`
- A facultative base controller and a setup `twig/twig` instance
- A database setup with `illuminate/database`
- A command bus with `league/tactician`
- Logs handling with `monolog/monolog`
- A debugbar with `maximebf/debugbar`
- A small CLi with `symfony/console`
- Migrations through `robmorgan/phinx`

Any of these can be overidden or removed; this package doesn't enforce any structure or the use of any dependency in particular besides `league/container` (as the Application class expects service provider capabilities).
