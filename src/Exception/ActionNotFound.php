<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller\Exception;

use Exception;
use RuntimeException;

/**
 * @package ActiveCollab\Id\Controller\Exception
 */
class ActionNotFound extends RuntimeException
{
    /**
     * @param string         $controller
     * @param string         $action
     * @param Exception|null $previous
     */
    public function __construct($controller, $action, Exception $previous = null)
    {
        parent::__construct("Action '$action' not found in '$controller' controller.", 0, $previous);
    }
}
