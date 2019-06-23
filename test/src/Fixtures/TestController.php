<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test\Fixtures;

use ActiveCollab\Controller\Controller as BaseController;
use Psr\Http\Message\ServerRequestInterface;

class TestController extends BaseController
{
    private $before_should_return = null;

    public $is_configured = false;

    protected function configure(): void
    {
        $this->is_configured = true;
    }

    public function getBeforeShouldReturn()
    {
        return $this->before_should_return;
    }

    public function &setBeforeShouldReturn($before_should_return)
    {
        $this->before_should_return = $before_should_return;

        return $this;
    }

    public function __before(ServerRequestInterface $request)
    {
        return $this->before_should_return;
    }

    public function index()
    {
        return [1, 2, 3];
    }
}
