<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Definitions;

use Interop\Container\Definition\DefinitionProviderInterface;
use League\Route\RouteCollection;
use Madewithlove\Glue\Definitions\DefinitionTypes\ExtendDefinition;
use Zend\Diactoros\Response\HtmlResponse;

class DemoDefinition implements DefinitionProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinitions()
    {
        $routes = new ExtendDefinition(RouteCollection::class);
        $routes->addMethodCall('get', '/', [$this, 'welcomeScreen']);

        return [$routes];
    }

    /**
     * @return string
     */
    public function welcomeScreen()
    {
        return new HtmlResponse(<<<'HTML'
<!doctype html>
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
            align-content: center;;
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
