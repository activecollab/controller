<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller;

use ActiveCollab\ContainerAccess\ContainerAccessInterface;
use ActiveCollab\ContainerAccess\ContainerAccessInterface\Implementation as ContainerAccessInterfaceImplementation;
use ActiveCollab\Controller\Exception\ActionForMethodNotFound;
use ActiveCollab\Controller\Exception\ActionNotFound;
use ActiveCollab\Controller\Response\StatusResponse;
use ActiveCollab\Controller\ResultEncoder\ResultEncoderInterface;
use Exception;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Controller
 */
abstract class Controller implements ContainerAccessInterface, ControllerInterface
{
    use ContainerAccessInterfaceImplementation;

    /**
     * @var ResultEncoderInterface
     */
    private $result_encoder;

    /**
     * @param ContainerInterface     $container
     * @param ResultEncoderInterface $result_encoder
     */
    public function __construct(ContainerInterface &$container, ResultEncoderInterface &$result_encoder)
    {
        $this->setContainer($container);
        $this->setResultEncoder($result_encoder);
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
                        return $this->getResultEncoder()->encode($e, $request, $response);
                    }
                }
            } else {
                throw new ActionNotFound(get_class($this), $action);
            }
        } else {
            throw new ActionForMethodNotFound($request->getMethod());
        }
    }

    /**
     * Return a param from a parsed body.
     *
     * This method is NULL or object safe - it will check for body type, and do it's best to return a value without
     * breaking or throwing a warning.
     *
     * @param  ServerRequestInterface $request
     * @param                         $param_name
     * @param  null                   $default
     * @return mixed|null
     */
    protected function getParsedBodyParam(ServerRequestInterface $request, $param_name, $default = null)
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

    /**
     * @param  ServerRequestInterface $request
     * @param  string                 $param_name
     * @param  mixed                  $default
     * @return mixed
     */
    protected function getCookieParam(ServerRequestInterface $request, $param_name, $default = null)
    {
        return array_key_exists($param_name, $request->getCookieParams()) ? $request->getCookieParams()[$param_name] : $default;
    }

    /**
     * @param  ServerRequestInterface $request
     * @param  string                 $param_name
     * @param  mixed                  $default
     * @return mixed
     */
    protected function getQueryParam(ServerRequestInterface $request, $param_name, $default = null)
    {
        return array_key_exists($param_name, $request->getQueryParams()) ? $request->getQueryParams()[$param_name] : $default;
    }

    /**
     * @param  ServerRequestInterface $request
     * @param  string                 $param_name
     * @param  mixed                  $default
     * @return mixed
     */
    protected function getServerParam(ServerRequestInterface $request, $param_name, $default = null)
    {
        return array_key_exists($param_name, $request->getServerParams()) ? $request->getServerParams()[$param_name] : $default;
    }
}
