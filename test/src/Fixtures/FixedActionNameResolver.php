<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test\Fixtures;

use ActiveCollab\Controller\ActionNameResolver\ActionNameResolverInterface;
use Exception;
use Psr\Http\Message\ServerRequestInterface;

class FixedActionNameResolver implements ActionNameResolverInterface
{
    private $what_to_return;

    public function __construct($what_to_return)
    {
        $this->what_to_return = $what_to_return;
    }

    public function getActionName(ServerRequestInterface $request): string
    {
        if ($this->what_to_return instanceof Exception) {
            throw $this->what_to_return;
        }

        return $this->what_to_return;
    }
}
