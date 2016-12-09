<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test\Fixtures;

use ActiveCollab\Controller\Controller as BaseController;

class TestController extends BaseController
{
    public function index()
    {
        return [1, 2, 3];
    }
}
