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
use ActiveCollab\Controller\ResultEncoder\ResultEncoderInterface;
use Exception;
use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

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
     * @var ResultEncoderInterface
     */
    private $result_encoder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $action_exception_message = 'Whoops, something went wrong...';

    /**
     * @var string
     */
    private $log_exception_message = 'Controller action aborted with an exception';

    /**
     * @param ContainerInterface     $container
     * @param ResultEncoderInterface $result_encoder
     * @param LoggerInterface|null   $logger
     */
    public function __construct(ContainerInterface &$container, ResultEncoderInterface &$result_encoder, LoggerInterface $logger = null)
    {
        $this->setContainer($container);
        $this->setResultEncoder($result_encoder);
        $this->setLogger($logger);
    }

    /**
     * {@inheritdoc}
     */
    public function getActionNameResolver()
    {
        return $this->action_name_resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function &setActionNameResolver(ActionNameResolverInterface $action_name_resolver)
    {
        $this->action_name_resolver = $action_name_resolver;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultEncoder()
    {
        return $this->result_encoder;
    }

    /**
     * {@inheritdoc}
     */
    public function &setResultEncoder(ResultEncoderInterface $result_encoder)
    {
        $this->result_encoder = $result_encoder;

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
    public function &setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientFacingExceptionMessage()
    {
        return $this->action_exception_message;
    }

    /**
     * {@inheritdoc}
     */
    public function &setClientFacingExceptionMessage($message)
    {
        if (empty($message)) {
            throw new InvalidArgumentException("Client facing exception message can't be empty");
        }

        $this->action_exception_message = $message;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogExceptionMessage()
    {
        return $this->log_exception_message;
    }

    /**
     * {@inheritdoc}
     */
    public function &setLogExceptionMessage($message)
    {
        if (empty($message)) {
            throw new InvalidArgumentException("Log exception message can't be empty");
        }

        $this->log_exception_message = $message;

        return $this;
    }

    /**
     * Run before every action.
     *
     * @param  ServerRequestInterface $request
     * @param  array                  $arguments
     * @return StatusResponse|void
     */
    protected function __before(ServerRequestInterface $request, array $arguments)
    {
    }

    /**
     * @var string
     */
    private $controller_name;

    /**
     * Return controller name, without namespace.
     *
     * @return string
     */
    public function getControllerName()
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
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface      $response
     * @param  array                  $arguments
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $arguments = [])
    {
        if ($action = $request->getAttribute('route')->getArgument($request->getMethod() . '_action')) {
            if (method_exists($this, $action)) {
                $before_result = $this->__before($request, $arguments);

                if ($before_result instanceof StatusResponse) {
                    return $this->getResultEncoder()->encode($before_result, $request, $response);
                } else {
                    try {
                        return $this->getResultEncoder()->encode(call_user_func([&$this, $action], $request, $arguments), $request, $response);
                    } catch (Exception $e) {
                        if ($this->logger) {
                            $this->logger->error($this->getLogExceptionMessage(), ['exception' => $e]);
                        }

                        $exception_message = $this->action_exception_message;

                        if (strpos($exception_message, '{message}') !== false) {
                            $exception_message = str_replace('{message}', $e->getMessage(), $exception_message);
                        }

                        return $this->getResultEncoder()->encode(new RuntimeException($exception_message, 0, $e), $request, $response);
                    }
                }
            } else {
                throw new ActionNotFound(get_class($this), $action);
            }
        } else {
            throw new ActionForMethodNotFound($request->getMethod());
        }
    }
}
