<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultEncoder\ValueEncoder;

use Psr\Http\Message\ResponseInterface;
use Throwable;

class ErrorEncoder implements ValueEncoderInterface
{
    use JsonContentTypeTrait;

    private $display_error_details;

    public function __construct(bool $display_error_details = false)
    {
        $this->display_error_details = $display_error_details;
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayErrorDetails()
    {
        return $this->display_error_details;
    }
    /**
     * {@inheritdoc}
     */
    public function &setDisplayErrorDetails($value)
    {
        $this->display_error_details = (bool) $value;

        return $this;
    }

    public function shouldEncode($value): bool
    {
        return $value instanceof Throwable;
    }

    /**
     * @param  ResponseInterface $response
     * @param  Throwable         $value
     * @return ResponseInterface
     */
    public function encode(ResponseInterface $response, $value): ResponseInterface
    {
        $response = $this->setJsonContentType($response);

        $error = ['message' => $value->getMessage(), 'type' => get_class($value)];
        if ($this->getDisplayErrorDetails()) {
            $error['exception'] = [];

            do {
                $error['exception'][] = [
                    'type' => get_class($value),
                    'code' => $value->getCode(),
                    'message' => $value->getMessage(),
                    'file' => $value->getFile(),
                    'line' => $value->getLine(),
                    'trace' => explode("\n", $value->getTraceAsString()),
                ];
            } while ($value = $value->getPrevious());
        }

        return $response->withStatus(500)->write(json_encode($error));
    }
}
