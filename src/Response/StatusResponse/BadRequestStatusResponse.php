<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Response\StatusResponse;

use ActiveCollab\Controller\ActionResult\StatusResult;

/**
 * @package ActiveCollab\Controller\Response\StatusResponse
 */
class BadRequestStatusResponse extends StatusResult
{
    /**
     * @param string $message
     * @param mixed  $payload
     */
    public function __construct($message = '', $payload = null)
    {
        if (empty($message)) {
            $message = 'Bad request';
        }

        parent::__construct(400, $message, $payload);
    }
}
