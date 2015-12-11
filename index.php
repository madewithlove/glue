<?php
use Madewithlove\Glue\Definitions\DemoDefinition;
use Madewithlove\Glue\Glue;

require 'vendor/autoload.php';

$app = new Glue();
$app->setDefinitionProvider('demo', new DemoDefinition());

$app->run();
