<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller\Response;

use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;

/**
 * @package ActiveCollab\Controller\Response
 */
interface ViewResponseInterface extends ResponseInterface
{
    /**
     * Render a template to a response.
     *
     * @param  Psr7ResponseInterface $response
     * @return Psr7ResponseInterface
     */
    public function render(Psr7ResponseInterface $response);
}
