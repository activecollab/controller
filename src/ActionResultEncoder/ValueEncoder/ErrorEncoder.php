<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultEncoder\ValueEncoder;

use ActiveCollab\Controller\ActionResultEncoder\ActionResultEncoderInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ErrorEncoder extends ValueEncoder
{
    use JsonContentTypeTrait;

    private $display_error_details;

    public function __construct(bool $display_error_details = false)
    {
        parent::__construct();

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
     * @param  ResponseInterface            $response
     * @param  ActionResultEncoderInterface $encoder
     * @param  Throwable                    $value
     * @return ResponseInterface
     */
    public function encode(ResponseInterface $response, ActionResultEncoderInterface $encoder, $value): ResponseInterface
    {
        $response = $this->setJsonContentType($response);

        $data_to_encode = $this->exceptionToArray($value);

        if ($value->getPrevious()) {
            $data_to_encode['previous'] = $this->exceptionToArray($value->getPrevious());
        }

        return $response->withStatus(500)->withBody($this->createBodyFromText(json_encode($data_to_encode)));
    }

    /**
     * @param  \Exception|\Throwable $exception
     * @return array
     */
    private function exceptionToArray($exception)
    {
        $result = [
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
        ];

        if ($this->getDisplayErrorDetails()) {
            $result = array_merge($result, [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);
        }

        return $result;
    }
}
