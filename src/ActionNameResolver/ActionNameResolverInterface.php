<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller\ActionNameResolver;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface ActionNameResolverInterface.
 *
 * @package ActiveCollab\Controller\ActionNameResolver
 */
interface ActionNameResolverInterface
{
    /**
     * Return action name from the given request object.
     *
     * @param  ServerRequestInterface $request
     * @return string
     */
    public function getActionName(ServerRequestInterface $request);
}
