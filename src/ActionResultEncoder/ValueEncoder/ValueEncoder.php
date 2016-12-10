<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultEncoder\ValueEncoder;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Slim\Http\Stream;

abstract class ValueEncoder implements ValueEncoderInterface
{
    /**
     * Create the message body.
     *
     * @param  string|StreamInterface   $text
     * @return StreamInterface
     * @throws InvalidArgumentException if $html is neither a string or stream
     */
    protected function createBodyFromText($text): StreamInterface
    {
        if ($text instanceof StreamInterface) {
            return $text;
        }

        if (!is_string($text)) {
            throw new InvalidArgumentException(sprintf('Invalid content (%s) provided to %s', (is_object($text) ? get_class($text) : gettype($text)), __CLASS__));
        }

        $handle = fopen('php://temp', 'wb+');

        $body = new Stream($handle);
        $body->write($text);
        $body->rewind();

        return $body;
    }
}
