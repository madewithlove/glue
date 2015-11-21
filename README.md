# Glue
[![Build Status](https://travis-ci.org/madewithlove/glue.svg)](https://travis-ci.org/madewithlove/glue)

Glue is an helper package made to quickly bootstrap packages-based applications. At its core it's just a container and a quick PSR7 setup, on top of which are glued together service providers and middlewares.

It is _not_ a microframework, if this is what you're looking for I recommend instead using [Silex], [Slim] or [Aura.Web].
On the contrary, Glue is as its name indicate just a bit of glue to tie existing packages and middlewares together.
It doesn't assume much, it won't get in your way, it's just a way to tie packages together in a nano web app.

## What's in the box

Glue comes with several providers by default:
- A base routing system using `league/route`
- A PSR7 stack using `zendframework/zend-diactoros`
- A facultative base controller and a setup `twig/twig` instance
- A database setup with `illuminate/database`
- A command bus with `league/tactician`
- Logs handling with `monolog/monolog`
- A debugbar with `maximebf/debugbar`
- A small CLi with `symfony/console`
- Migrations through `robmorgan/phinx`

Any of these can be overidden or removed; this package doesn't enforce any structure or the use of any dependency in particular besides `league/container` (as the Glue class expects service provider capabilities), so you can make of it whatever you wish.

[Silex]: http://silex.sensiolabs.org/
[Slim]: http://www.slimframework.com/
[Aura.Web]: http://auraphp.com/
