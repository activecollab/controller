<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultEncoder\ValueEncoder;

interface ActionResultEncoderInterface
{
    public function &addValueEncoder(ValueEncoderInterface $value_encoder): ActionResultEncoderInterface;
}
