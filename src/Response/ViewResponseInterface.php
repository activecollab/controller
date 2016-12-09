<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Response;

use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;

interface ViewResponseInterface extends ResponseInterface
{
    public function render(Psr7ResponseInterface $response): Psr7ResponseInterface;
}
