<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test\Fixtures;

use ActiveCollab\Controller\ActionResult\Container\ActionResultContainerInterface;
use ActiveCollab\ValueContainer\ValueContainerInterface;
use LogicException;
use Pimple\Container;
use RuntimeException;

class ActionResultInContainer implements ActionResultContainerInterface
{
    private Container $container;
    private string $key;

    public function __construct(Container $container, string $key = 'action_result')
    {
        $this->container = $container;
        $this->key = $key;
    }

    public function getValue()
    {
        if ($this->hasValue()) {
            return $this->container[$this->key];
        }

        throw new RuntimeException('Action result not found in the container.');
    }

    public function hasValue(): bool
    {
        return $this->container->offsetExists($this->key);
    }

    public function setValue($value): ValueContainerInterface
    {
        $this->container[$this->key] = $value;

        return $this;
    }

    public function removeValue(): ValueContainerInterface
    {
        throw new LogicException("Value can't be removed.");
    }
}
