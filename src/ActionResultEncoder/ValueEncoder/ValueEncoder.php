<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultEncoder\ValueEncoder;

use Laminas\Diactoros\StreamFactory;
use Psr\Http\Message\StreamInterface;

abstract class ValueEncoder implements ValueEncoderInterface
{
    public function __construct()
    {
    }

    /**
     * Create the message body.
     *
     * @param  string          $text
     * @return StreamInterface
     */
    protected function createBodyFromText(string $text): StreamInterface
    {
        return (new StreamFactory())->createStream($text);
    }
}
