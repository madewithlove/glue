<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Dummies;

use Madewithlove\Glue\Http\Controllers\AbstractController;

class DummyAbstractController extends AbstractController
{
    public function index()
    {
        return $this->render('index.twig', [
            'foo' => 'bar',
        ]);
    }

    public function show()
    {
        return $this->dispatch(new DummyCommand('foobar'));
    }
}
