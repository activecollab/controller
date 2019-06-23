<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test\Fixtures;

class ArrayAsJson implements \JsonSerializable
{
    private $array;

    public function __construct(array $array = [])
    {
        $this->array = $array;
    }

    public function jsonSerialize()
    {
        return $this->array;
    }
}
