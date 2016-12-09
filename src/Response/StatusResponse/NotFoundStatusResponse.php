<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Response\StatusResponse;

use ActiveCollab\Controller\Response\StatusResponse;

/**
 * @package ActiveCollab\Controller\Response\StatusResponse
 */
class NotFoundStatusResponse extends StatusResponse
{
    /**
     * @param string $message
     */
    public function __construct($message = '')
    {
        if (empty($message)) {
            $message = 'Not found';
        }

        parent::__construct(404, $message);
    }
}
