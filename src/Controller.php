<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller;

use ActiveCollab\ContainerAccess\ContainerAccessInterface;
use ActiveCollab\ContainerAccess\ContainerAccessInterface\Implementation as ContainerAccessInterfaceImplementation;
use ActiveCollab\Controller\ActionNameResolver\ActionNameResolverInterface;
use ActiveCollab\Controller\ActionResult\Container\ActionResultContainerInterface;
use ActiveCollab\Controller\ActionResult\MovedResult\MovedResult;
use ActiveCollab\Controller\ActionResult\MovedResult\MovedResultInterface;
use ActiveCollab\Controller\ActionResult\StatusResult\StatusResult;
use ActiveCollab\Controller\ActionResult\StatusResult\StatusResultInterface;
use ActiveCollab\Controller\CommonResults\CommonResultsInterface;
use ActiveCollab\Controller\Exception\ActionForMethodNotFound;
use ActiveCollab\Controller\Exception\ActionNotFound;
use ActiveCollab\Controller\RequestParamGetter\RequestParamGetterInterface;
use Exception;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Throwable;

/**
 * @package ActiveCollab\Controller
 */
abstract class Controller implements ContainerAccessInterface, ControllerInterface, RequestParamGetterInterface, CommonResultsInterface
{
    use ContainerAccessInterfaceImplementation;

    private $action_name_resolver;

    /**
     * @var ActionResultContainerInterface
     */
    private $action_result_container;

    private $logger;

    private $client_safe_exception_message = 'Whoops, something went wrong...';

    private $log_exception_message = 'Controller action aborted due to an exception.';

    private $log_php_error_message = 'Controller action aborted due to a PHP error.';

    public function __construct(ActionNameResolverInterface $action_name_resolver, ActionResultContainerInterface $action_result_container, LoggerInterface $logger = null)
    {
        $this->setActionNameResolver($action_name_resolver);
        $this->setActionResultContainer($action_result_container);
        $this->setLogger($logger);

        $this->configure();
    }

    protected function configure(): void
    {
    }

    public function __before(ServerRequestInterface $request)
    {
        return null;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response, callable $next = null
    ): ResponseInterface
    {
        try {
            $action_name = $this->getActionNameResolver()->getActionName($request);
        } catch (RuntimeException $e) {
            throw new ActionForMethodNotFound($request->getMethod(), $request->getUri()->getPath(), $e);
        }

        if (empty($action_name)) {
            throw new ActionForMethodNotFound($request->getMethod(), $request->getUri()->getPath());
        }

        if (!method_exists($this, $action_name)) {
            throw new ActionNotFound(get_class($this), $action_name);
        }

        // Run __before() method prior to running the action.
        $action_result = $this->__before($request);

        // If __before() did not exist with a status response, call the action.
        if ($action_result === null) {
            try {
                $action_result = call_user_func([&$this, $action_name], $request);
            } catch (Exception $exception) {
                $action_result = $this->handleException($exception);
            } catch (Throwable $php_error) {
                $action_result = $this->handlePhpError($php_error);
            }
        }

        $this->getActionResultContainer()->setValue($action_result);

        if ($next) {
            $response = $next($request, $response);
        }

        return $response;
    }

    private $controller_name;

    public function getControllerName(): string
    {
        if (empty($this->controller_name)) {
            $controller_class = get_class($this);

            if (($pos = strrpos($controller_class, '\\')) !== false) {
                $this->controller_name = substr($controller_class, $pos + 1);
            } else {
                $this->controller_name = $controller_class;
            }
        }

        return $this->controller_name;
    }

    public function getActionNameResolver(): ActionNameResolverInterface
    {
        return $this->action_name_resolver;
    }

    public function &setActionNameResolver(ActionNameResolverInterface $action_name_resolver): ControllerInterface
    {
        $this->action_name_resolver = $action_name_resolver;

        return $this;
    }

    public function getActionResultContainer(): ActionResultContainerInterface
    {
        return $this->action_result_container;
    }

    public function &setActionResultContainer(
        ActionResultContainerInterface $action_result_container
    ): ControllerInterface
    {
        $this->action_result_container = $action_result_container;

        return $this;
    }

    public function getLogger(): ? LoggerInterface
    {
        return $this->logger;
    }

    public function &setLogger(LoggerInterface $logger = null): ControllerInterface
    {
        $this->logger = $logger;

        return $this;
    }

    public function getClientSafeExceptionMessage(): string
    {
        return $this->client_safe_exception_message;
    }

    public function &setClientSafeExceptionMessage(string $message): ControllerInterface
    {
        if (empty($message)) {
            throw new InvalidArgumentException("Client safe exception message can't be empty.");
        }

        $this->client_safe_exception_message = $message;

        return $this;
    }

    public function getLogExceptionMessage(): string
    {
        return $this->log_exception_message;
    }

    public function &setLogExceptionMessage(string $message): ControllerInterface
    {
        if (empty($message)) {
            throw new InvalidArgumentException("Log exception message can't be empty.");
        }

        $this->log_exception_message = $message;

        return $this;
    }

    public function getLogPhpErrorMessage(): string
    {
        return $this->log_php_error_message;
    }

    public function &setLogPhpErrorMessage(string $message): ControllerInterface
    {
        if (empty($message)) {
            throw new InvalidArgumentException("Log PHP error message can't be empty.");
        }

        $this->log_php_error_message = $message;

        return $this;
    }

    private function handleException(Exception $exception): Exception
    {
        if ($this->getLogger()) {
            $this->getLogger()->error($this->getLogExceptionMessage(), [
                'exception' => $exception,
            ]);
        }

        $exception_message = $this->prepareClientSafeErrorMessage($exception);

        return new RuntimeException($exception_message, 0, $exception);
    }

    private function handlePhpError(Throwable $php_error): Exception
    {
        if ($this->getLogger()) {
            $this->getLogger()->error($this->getLogPhpErrorMessage(), [
                'exception' => $php_error,
            ]);
        }

        $exception_message = $this->prepareClientSafeErrorMessage($php_error);

        return new RuntimeException($exception_message, 0, $php_error);
    }

    private function prepareClientSafeErrorMessage(Throwable $error): string
    {
        $exception_message = $this->getClientSafeExceptionMessage();

        if (strpos($exception_message, '{message}') !== false) {
            $exception_message = str_replace('{message}', $error->getMessage(), $exception_message);
        }

        return $exception_message;
    }

    public function getParsedBodyParam(ServerRequestInterface $request, string $param_name, $default = null)
    {
        $parsed_body = $request->getParsedBody();

        if ($parsed_body) {
            if (is_array($parsed_body) && array_key_exists($param_name, $parsed_body)) {
                return $parsed_body[$param_name];
            } elseif (is_object($parsed_body) && property_exists($parsed_body, $param_name)) {
                return $parsed_body->$param_name;
            }
        }

        return $default;
    }

    public function getCookieParam(ServerRequestInterface $request, string $param_name, $default = null)
    {
        return array_key_exists($param_name, $request->getCookieParams())
            ? $request->getCookieParams()[$param_name]
            : $default;
    }

    public function getQueryParam(ServerRequestInterface $request, $param_name, $default = null)
    {
        return array_key_exists($param_name, $request->getQueryParams())
            ? $request->getQueryParams()[$param_name]
            : $default;
    }

    public function getServerParam(ServerRequestInterface $request, string $param_name, $default = null)
    {
        return array_key_exists($param_name, $request->getServerParams())
            ? $request->getServerParams()[$param_name]
            : $default;
    }

    public function ok(string $message = ''): StatusResultInterface
    {
        return new StatusResult(200, $message);
    }

    public function created($payload = null): StatusResultInterface
    {
        return new StatusResult(201, '', $payload);
    }

    public function badRequest(string $message = ''): StatusResultInterface
    {
        return new StatusResult(400, $message);
    }

    public function forbidden(string $message = ''): StatusResultInterface
    {
        return new StatusResult(403, $message);
    }

    public function notFound(string $message = ''): StatusResultInterface
    {
        return new StatusResult(404, $message);
    }

    public function conflict(string $message = ''): StatusResultInterface
    {
        return new StatusResult(409, $message);
    }

    public function moved(string $url, bool $is_moved_permanently = false): MovedResultInterface
    {
        return new MovedResult($url, $is_moved_permanently);
    }
}
