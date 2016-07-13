<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller;

use ActiveCollab\Controller\ResultEncoder\ResultEncoderInterface;
use Psr\Log\LoggerInterface;

/**
 * @package ActiveCollab\Controller
 */
interface ControllerInterface
{
    /**
     * Return action result encoder.
     *
     * @return ResultEncoderInterface
     */
    public function getResultEncoder();

    /**
     * Set action result encoder.
     *
     * @param  ResultEncoderInterface $result_encoder
     * @return $this
     */
    public function &setResultEncoder(ResultEncoderInterface $result_encoder);

    /**
     * Return logger.
     *
     * @return LoggerInterface|null
     */
    public function getLogger();

    /**
     * Set logger.
     *
     * @param  LoggerInterface|null $logger
     * @return $this
     */
    public function &setLogger(LoggerInterface $logger = null);

    /**
     * Return message that is returned as 500 error when action breaks due to an exception.
     *
     * @return string
     */
    public function getClientFacingExceptionMessage();

    /**
     * Set message that is returned as 500 error when action breaks due to exception.
     *
     * If you wish to include actual exception's message, add {message} to the test. Example:
     *
     * $controller->setActionExceptionMessage('Failed because {message}');
     *
     * @param  string $message
     * @return $this
     */
    public function &setClientFacingExceptionMessage($message);

    /**
     * Return message that will be logged if contraoller action fails due to an exception.
     *
     * @return string
     */
    public function getLogExceptionMessage();

    /**
     * Set message that will be logged if contraoller action fails due to an exception.
     *
     * @param  string $message
     * @return $this
     */
    public function &setLogExceptionMessage($message);

    /**
     * Return controller name, without namespace.
     *
     * @return string
     */
    public function getControllerName();
}
