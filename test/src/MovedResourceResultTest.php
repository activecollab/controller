<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test;

use ActiveCollab\Controller\ActionResult\MovedResult\MovedResult;
use ActiveCollab\Controller\Test\Base\TestCase;
use InvalidArgumentException;

class MovedResourceResultTest extends TestCase
{
    public function testExceptionOnInvalidUrl()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Value 'not an url' is not a valid URL.");

        new MovedResult('not an url');
    }
}
