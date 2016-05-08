<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller\Response;

/**
 * @package ActiveCollab\Controller\Response\StatusResponse
 */
class StatusResponse implements ResponseInterface
{
    /**
     * @var int
     */
    private $http_code;

    /**
     * @var string
     */
    private $message;

    /**
     * @param int    $http_code
     * @param string $message
     */
    public function __construct($http_code, $message = '')
    {
        $this->http_code = $http_code;
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return $this->http_code;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
