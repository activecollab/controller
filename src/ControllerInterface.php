<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller;

use ActiveCollab\Controller\ActionNameResolver\ActionNameResolverInterface;
use ActiveCollab\Controller\ActionResult\Container\ActionResultContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * @package ActiveCollab\Controller
 */
interface ControllerInterface
{
    public function __before(ServerRequestInterface $request);

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null): ResponseInterface;

    public function getControllerName(): string;

    public function getActionNameResolver(): ActionNameResolverInterface;

    public function &setActionNameResolver(ActionNameResolverInterface $action_name_resolver): ControllerInterface;

    public function getActionResultContainer(): ActionResultContainerInterface;

    public function &setActionResultContainer(ActionResultContainerInterface $action_result_container): ControllerInterface;

    public function getLogger();

    public function &setLogger(LoggerInterface $logger = null): ControllerInterface;

    public function getClientSafeExceptionMessage(): string;

    public function &setClientSafeExceptionMessage(string $message): ControllerInterface;

    public function getLogExceptionMessage(): string;

    public function &setLogExceptionMessage(string $message): ControllerInterface;

    public function getLogPhpErrorMessage(): string;

    public function &setLogPhpErrorMessage(string $message): ControllerInterface;
}
