<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller;

use ActiveCollab\ContainerAccess\ContainerAccessInterface;
use ActiveCollab\ContainerAccess\ContainerAccessInterface\Implementation as ContainerAccessInterfaceImplementation;
use ActiveCollab\Controller\ActionNameResolver\ActionNameResolverInterface;
use ActiveCollab\Controller\Exception\ActionForMethodNotFound;
use ActiveCollab\Controller\Exception\ActionNotFound;
use ActiveCollab\Controller\RequestParamGetter\Implementation as RequestParamGetterImplementation;
use ActiveCollab\Controller\RequestParamGetter\RequestParamGetterInterface;
use ActiveCollab\Controller\Response\StatusResponse;
use Exception;
use InvalidArgumentException;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Throwable;

/**
 * @package ActiveCollab\Controller
 */
abstract class Controller implements ContainerAccessInterface, ControllerInterface, RequestParamGetterInterface
{
    use ContainerAccessInterfaceImplementation, RequestParamGetterImplementation;

    /**
     * @var ActionNameResolverInterface
     */
    private $action_name_resolver;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $client_safe_exception_message = 'Whoops, something went wrong...';

    /**
     * @var string
     */
    private $log_exception_message = 'Controller action aborted due to an exception.';

    /**
     * @var string
     */
    private $log_php_error_message = 'Controller action aborted due to a PHP error.';

    /**
     * @param ActionNameResolverInterface $action_name_resolver
     * @param LoggerInterface|null        $logger
     */
    public function __construct(ActionNameResolverInterface $action_name_resolver, LoggerInterface $logger = null)
    {
        $this->setActionNameResolver($action_name_resolver);
        $this->setLogger($logger);
    }

    /**
     * {@inheritdoc}
     */
    protected function __before(ServerRequestInterface $request)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        try {
            $action_name = $this->getActionNameResolver()->getActionName($request);
        } catch (RuntimeException $e) {
            throw new ActionForMethodNotFound($request->getMethod(), $e);
        }

        if (empty($action_name)) {
            throw new LogicException('Controller action name cannot be empty.');
        }

        if (!method_exists($this, $action_name)) {
            throw new ActionNotFound(get_class($this), $action_name);
        }

        // Run __before() method prior to running the action.
        $action_result = $this->__before($request);

        // If __before() did not exist with a status response, call the action.
        if (!$action_result instanceof StatusResponse) {
            try {
                $action_result = call_user_func([&$this, $action_name], $request);
            } catch (Exception $exception) {
                $action_result = $this->handleException($exception);
            } catch (Throwable $php_error) {
                $action_result = $this->handlePhpError($php_error);
            }
        }

        $request = $request->withAttribute('action_result', $action_result);

        if ($next) {
            $response = $next($request, $response);
        }

        return $response;
    }

    /**
     * @var string
     */
    private $controller_name;

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getActionNameResolver(): ActionNameResolverInterface
    {
        return $this->action_name_resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function &setActionNameResolver(ActionNameResolverInterface $action_name_resolver): ControllerInterface
    {
        $this->action_name_resolver = $action_name_resolver;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * {@inheritdoc}
     */
    public function &setLogger(LoggerInterface $logger = null): ControllerInterface
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientSafeExceptionMessage(): string
    {
        return $this->client_safe_exception_message;
    }

    /**
     * {@inheritdoc}
     */
    public function &setClientSafeExceptionMessage(string $message): ControllerInterface
    {
        if (empty($message)) {
            throw new InvalidArgumentException("Client safe exception message can't be empty.");
        }

        $this->client_safe_exception_message = $message;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogExceptionMessage(): string
    {
        return $this->log_exception_message;
    }

    /**
     * {@inheritdoc}
     */
    public function &setLogExceptionMessage(string $message): ControllerInterface
    {
        if (empty($message)) {
            throw new InvalidArgumentException("Log exception message can't be empty.");
        }

        $this->log_exception_message = $message;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogPhpErrorMessage(): string
    {
        return $this->log_php_error_message;
    }

    /**
     * {@inheritdoc}
     */
    public function &setLogPhpErrorMessage(string $message): ControllerInterface
    {
        if (empty($message)) {
            throw new InvalidArgumentException("Log PHP error message can't be empty.");
        }

        $this->log_php_error_message = $message;

        return $this;
    }

    /**
     * @param  Exception $exception
     * @return Exception
     */
    private function handleException(Exception $exception): Exception
    {
        if ($this->logger) {
            $this->logger->error($this->getLogExceptionMessage(), [
                'exception' => $exception,
            ]);
        }

        $exception_message = $this->prepareClientSafeErrorMessage($exception);

        return new RuntimeException($exception_message, 0, $exception);
    }

    /**
     * @param  Throwable $php_error
     * @return Exception
     */
    private function handlePhpError(Throwable $php_error): Exception
    {
        if ($this->logger) {
            $this->logger->error($this->getLogExceptionMessage(), [
                'exception' => $php_error,
            ]);
        }

        $exception_message = $this->prepareClientSafeErrorMessage($php_error);

        return new RuntimeException($exception_message, 0, $php_error);
    }

    /**
     * @param  Throwable $error
     * @return string
     */
    private function prepareClientSafeErrorMessage(Throwable $error): string
    {
        $exception_message = $this->getClientSafeExceptionMessage();

        if (strpos($exception_message, '{message}') !== false) {
            $exception_message = str_replace('{message}', $error->getMessage(), $exception_message);
        }

        return $exception_message;
    }
}
