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

class MovedResourceResultTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Value 'not an url' is not a valid URL.
     */
    public function testExceptionOnInvalidUrl()
    {
        new MovedResult('not an url');
    }
}
