<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller\Exception;

use Exception;
use RuntimeException;

class ActionForMethodNotFound extends RuntimeException
{
    public function __construct(string $method, string $path, Exception $previous = null)
    {
        parent::__construct("Action for {$method} {$path} not found.", 0, $previous);
    }
}
