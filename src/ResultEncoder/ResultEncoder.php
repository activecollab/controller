<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller\ResultEncoder;

use ActiveCollab\Controller\Response\FileDownloadResponse;
use ActiveCollab\Controller\Response\StatusResponse;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

/**
 * @package ActiveCollab\Id\Controller\Base
 */
class ResultEncoder implements ResultEncoderInterface
{
    /**
     * @var bool
     */
    private $display_error_details = false;

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

    /**
     * {@inheritdoc}
     */
    public function encode($action_result, ServerRequestInterface $request, ResponseInterface $response)
    {
        if ($action_result instanceof FileDownloadResponse) {
            return $response; // @TODO handle this special case
        }

        if (empty($response->getHeader('content-type'))) {
            $response = $response->withHeader('Content-Type', 'application/json;charset=UTF-8');
        }

        // NULL
        if ($action_result === null) {
            return $response;

        // Respond with a status code
        } elseif ($action_result instanceof StatusResponse) {
            $response = $this->encodeStatus($action_result, $response);

            if ($action_result->getHttpCode() >= 400) {
                $response = $response->write(json_encode(['message' => $action_result->getMessage()]));
            }

            return $response;

        // Array
        } elseif (is_array($action_result)) {
            return $response->write(json_encode($action_result))->withStatus(200);

        // Exception
        } elseif ($action_result instanceof Throwable || $action_result instanceof Exception) {
            return $this->encodeException($action_result, $response);
        } else {
            return $this->onNoEncoderApplied($action_result, $request, $response);
        }
    }

    /**
     * Call this function when no response encoding is applied by the encode() method.
     *
     * @param  mixed                  $action_result
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface      $response
     * @return ResponseInterface
     */
    protected function onNoEncoderApplied($action_result, ServerRequestInterface $request, ResponseInterface $response)
    {
        return $response;
    }

    /**
     * Encode and return status response.
     *
     * @param  StatusResponse    $action_result
     * @param  ResponseInterface $response
     * @return ResponseInterface
     */
    private function encodeStatus(StatusResponse $action_result, ResponseInterface $response)
    {
        return $response->withStatus($action_result->getHttpCode(), $action_result->getMessage());
    }

    /**
     * Encode and return exception.
     *
     * @param  Throwable|Exception $exception
     * @param  ResponseInterface   $response
     * @return ResponseInterface
     */
    private function encodeException($exception, ResponseInterface $response)
    {
        $error = ['message' => $exception->getMessage(), 'type' => get_class($exception)];

        if ($this->getDisplayErrorDetails()) {
            $error['exception'] = [];

            do {
                $error['exception'][] = [
                    'type' => get_class($exception),
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => explode("\n", $exception->getTraceAsString()),
                ];
            } while ($exception = $exception->getPrevious());
        }

        return $response->write(json_encode($error))->withStatus(500);
    }
}
