<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller\ActionResult;

use InvalidArgumentException;

class MovedResource implements MovedResourceInterface
{
    private $url;

    private $is_moved_permanently;

    public function __construct(string $url, bool $is_moved_permanently = false)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("Value '$url' is not a valid URL.'");
        }

        $this->url = $url;
        $this->is_moved_permanently = $is_moved_permanently;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function isMovedPermanently(): bool
    {
        return $this->is_moved_permanently;
    }
}
