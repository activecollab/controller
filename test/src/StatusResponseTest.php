<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test;

use ActiveCollab\Controller\Response\StatusResponse;
use ActiveCollab\Controller\Response\StatusResponse\BadRequestStatusResponse;
use ActiveCollab\Controller\Test\Base\TestCase;

class StatusResponseTest extends TestCase
{
    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Status response is not an acceptible payload.
     */
    public function testStatusResponseIsNotAcceptablePayload()
    {
        new StatusResponse(200, 'Ok', new BadRequestStatusResponse());
    }
}
