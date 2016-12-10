<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResult\StatusResult;

use ActiveCollab\Controller\ActionResult\StatusResult;

/**
 * @package ActiveCollab\Controller\Response\StatusResponse
 */
class Created extends StatusResult
{
    /**
     * @param string $message
     * @param mixed  $payload
     */
    public function __construct($message = '', $payload = null)
    {
        if (empty($message)) {
            $message = 'Created';
        }

        parent::__construct(201, $message, $payload);
    }
}
