<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test;

use ActiveCollab\Controller\ActionResult\StatusResult;
use ActiveCollab\Controller\ActionResult\StatusResult\BadRequest;
use ActiveCollab\Controller\Test\Base\TestCase;

class StatusResultTest extends TestCase
{
    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Status response is not an acceptible payload.
     */
    public function testStatusResponseIsNotAcceptablePayload()
    {
        new StatusResult(200, 'Ok', new BadRequest());
    }
}
