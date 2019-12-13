<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\CommonResults;

use ActiveCollab\Controller\ActionResult\MovedResult\MovedResultInterface;
use ActiveCollab\Controller\ActionResult\StatusResult\StatusResultInterface;

interface CommonResultsInterface
{
    public function ok(string $message = ''): StatusResultInterface;
    public function created($payload = null): StatusResultInterface;
    public function badRequest(string $message = ''): StatusResultInterface;
    public function forbidden(string $message = ''): StatusResultInterface;
    public function notFound(string $message = ''): StatusResultInterface;
    public function conflict(string $message = ''): StatusResultInterface;
    public function moved(string $url, bool $is_moved_permanently = false): MovedResultInterface;
}
