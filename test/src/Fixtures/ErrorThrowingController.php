<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test\Fixtures;

use ActiveCollab\Controller\Controller as BaseController;
use LogicException;
use ParseError;

class ErrorThrowingController extends BaseController
{
    public function throwPhpError()
    {
        throw new ParseError('Error parsing our awesome code.');
    }

    public function throwException()
    {
        throw new LogicException('App logic is broken.');
    }
}
