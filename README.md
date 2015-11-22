# Glue
[![Build Status](https://travis-ci.org/madewithlove/glue.svg)](https://travis-ci.org/madewithlove/glue)

## What's Glue?

Glue is an adhesive substance used for sticking objects or materials together ( ͡° ͜ʖ ͡°)

Glue is also an helper package made to quickly bootstrap packages-based applications. At its core it's just a container and a quick PSR7 setup, on top of which are glued together service providers and middlewares.

This is _not_ a microframework (in the sense that it doesn't frame your work). If this is what you're looking for I recommend instead using [Silex], [Slim] or whatever you want.
On the contrary, Glue is as its name indicate just a bit of glue to tie existing packages and middlewares together.
It doesn't assume much, it won't get in your way, it's just a way to tie stuff together.

## What's in the box

Glue binds several providers out of the box:

- **Routing**
    - Base routing system with `league/route`
    - PSR7 stack with `zendframework/zend-diactoros`
    - View engine with `twig/twig`
    - Facultative base controller
- **Business**
    - Database handling with `illuminate/database`
    - Migrations with `robmorgan/phinx`
    - Command bus with `league/tactician`
- **Development**
    - Logs handling with `monolog/monolog`
    - Debugbar with `maximebf/debugbar`
    - Small CLI with `symfony/console`
    - Filesystem with `league/flysystem`

Any of these can be overidden or removed; this package doesn't enforce any structure or the use of any dependency in particular besides `league/container` (as the Glue class expects service provider capabilities), so you can make of it whatever you wish.

## But why then?

Because I do a lot of very small web applications, for myself or public ones, and I was tired of going through the same routine for the hundreth time. Then I thought others might have the same use case and here we are.

[Silex]: http://silex.sensiolabs.org/
[Slim]: http://www.slimframework.com/
