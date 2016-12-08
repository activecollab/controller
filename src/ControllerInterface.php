<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller;

use ActiveCollab\Controller\ActionNameResolver\ActionNameResolverInterface;
use Psr\Log\LoggerInterface;

/**
 * @package ActiveCollab\Controller
 */
interface ControllerInterface
{
    /**
     * Return controller name, without namespace.
     *
     * @return string
     */
    public function getControllerName(): string;

    /**
     * Return action name resolver.
     *
     * @return ActionNameResolverInterface
     */
    public function getActionNameResolver(): ActionNameResolverInterface;

    /**
     * Set action name resolver interface.
     *
     * @param  ActionNameResolverInterface $action_name_resolver
     * @return ControllerInterface|$this
     */
    public function &setActionNameResolver(ActionNameResolverInterface $action_name_resolver): ControllerInterface;

    /**
     * Return logger.
     *
     * @return LoggerInterface|null
     */
    public function getLogger();

    /**
     * Set logger.
     *
     * @param  LoggerInterface|null      $logger
     * @return ControllerInterface|$this
     */
    public function &setLogger(LoggerInterface $logger = null): ControllerInterface;

    /**
     * Return message that is returned as 500 error when action breaks due to an exception.
     *
     * @return string
     */
    public function getClientSafeExceptionMessage(): string;

    /**
     * Set message that is returned as 500 error when action breaks due to exception.
     *
     * If you wish to include actual exception's message, add {message} to the test. Example:
     *
     * $controller->setActionExceptionMessage('Failed because {message}');
     *
     * @param  string                    $message
     * @return ControllerInterface|$this
     */
    public function &setClientSafeExceptionMessage(string $message): ControllerInterface;

    /**
     * Return message that will be logged if controller action fails due to an exception.
     *
     * @return string
     */
    public function getLogExceptionMessage(): string;

    /**
     * Set message that will be logged if controller action fails due to an exception.
     *
     * @param  string                    $message
     * @return ControllerInterface|$this
     */
    public function &setLogExceptionMessage(string $message): ControllerInterface;

    /**
     * Return message that will be logged if controller action fails due to a PHP error.
     *
     * @return string
     */
    public function getLogPhpErrorMessage(): string;

    /**
     * Set message that will be logged if controller action fails due to a PHP error.
     *
     * @param  string                    $message
     * @return ControllerInterface|$this
     */
    public function &setLogPhpErrorMessage(string $message): ControllerInterface;
}
