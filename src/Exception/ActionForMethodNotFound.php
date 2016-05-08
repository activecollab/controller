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
class ActionForMethodNotFound extends RuntimeException
{
    /**
     * @param string         $method
     * @param Exception|null $previous
     */
    public function __construct($method, Exception $previous = null)
    {
        parent::__construct("Action for $method not found in route definition", 0, $previous);
    }
}
