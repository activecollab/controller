<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller\ActionResult\MovedResult;

interface MovedResultInterface
{
    public function getUrl(): string;

    public function isMovedPermanently(): bool;
}
