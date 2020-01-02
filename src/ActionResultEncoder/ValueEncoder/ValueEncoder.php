<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultEncoder\ValueEncoder;

use Psr\Http\Message\StreamInterface;
use Zend\Diactoros\Stream;

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
        $handle = fopen('php://temp', 'wb+');

        $body = new Stream($handle);
        $body->write($text);
        $body->rewind();

        return $body;
    }
}
