<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\ServiceProviders;

use Interop\Container\ServiceProviderInterface;
use League\Route\RouteCollection;
use Psr\Container\ContainerInterface;
use Zend\Diactoros\Response\HtmlResponse;

class DemoServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getServices()
    {
        return [
            RouteCollection::class => [$this, 'withDemoRoutes'],
        ];
    }

    /**
     * @param ContainerInterface $container
     * @param callable|null      $getPrevious
     *
     * @return RouteCollection
     */
    public function withDemoRoutes(ContainerInterface $container, callable $getPrevious = null)
    {
        /** @var RouteCollection $router */
        $router = $getPrevious();
        $router->get('/', [$this, 'welcomeScreen']);

        return $router;
    }

    /**
     * @return HtmlResponse
     */
    public function welcomeScreen()
    {
        return new HtmlResponse(<<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to Glue</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
        html, body, .wrapper {
            height: 100%;
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-content: center;
            background-color: #37A7F8;
        }

        h1 {
            font-weight: 100;
            font-size: 15rem;
            color: white
        }
    </style>
</head>
<body>
    <main class="wrapper">
        <h1 class="text-center">Welcome to Glue</h1>
    </main>
</body>
</html>
HTML
        );
    }
}
