<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultGetter;

use ActiveCollab\Controller\ActionResult\MovedResultInterface;
use ActiveCollab\Controller\ActionResult\StatusResultInterface;

interface ActionResultGetterInterface
{
    public function ok(): StatusResultInterface;

    public function created($payload = null): StatusResultInterface;

    public function badRequest(): StatusResultInterface;

    public function forbidden(): StatusResultInterface;

    public function notFound(): StatusResultInterface;

    public function moved(string $url, bool $is_moved_permanently): MovedResultInterface;
}
