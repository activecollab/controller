<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test;

use ActiveCollab\Controller\ActionResult\StatusResult\StatusResult;
use ActiveCollab\Controller\Test\Base\TestCase;
use LogicException;

class StatusResultTest extends TestCase
{
    public function testStatusResponseIsNotAcceptablePayload()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Status response is not an acceptable payload.");

        new StatusResult(200, 'Ok', new StatusResult(403));
    }
}
