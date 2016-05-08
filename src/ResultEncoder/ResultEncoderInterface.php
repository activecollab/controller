<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller\ResultEncoder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Controller\ResultEncoder
 */
interface ResultEncoderInterface
{
    /**
     * Return true if error details should be provided.
     *
     * @return bool
     */
    public function getDisplayErrorDetails();

    /**
     * Set whether error details should be provided.
     *
     * @param  bool  $value
     * @return $this
     */
    public function &setDisplayErrorDetails($value);

    /**
     * Prepare response based on action result.
     *
     * @param  mixed                  $action_result
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface      $response
     * @return ResponseInterface
     */
    public function encode($action_result, ServerRequestInterface $request, ResponseInterface $response);
}
