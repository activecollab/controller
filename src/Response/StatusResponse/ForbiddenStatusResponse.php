<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller\Response\StatusResponse;

use ActiveCollab\Controller\Response\StatusResponse;

/**
 * @package ActiveCollab\Controller\Response\StatusResponse
 */
class ForbiddenStatusResponse extends StatusResponse
{
    /**
     * @param string $message
     */
    public function __construct($message = '')
    {
        if (empty($message)) {
            $message = 'Forbidden';
        }

        parent::__construct(403, $message);
    }
}
