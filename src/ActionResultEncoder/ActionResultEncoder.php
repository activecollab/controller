<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultEncoder;

use ActiveCollab\ContainerAccess\ContainerAccessInterface;
use ActiveCollab\ContainerAccess\ContainerAccessInterface\Implementation as ContainerAccessImplementation;
use ActiveCollab\Controller\ActionResult\Container\ActionResultContainerInterface;
use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\ValueEncoderInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

class ActionResultEncoder implements ActionResultEncoderInterface, ContainerAccessInterface, MiddlewareInterface
{
    use ContainerAccessImplementation;

    /**
     * @var ActionResultContainerInterface
     */
    private $action_result_container;

    private $encode_on_exit = false;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ValueEncoderInterface[]
     */
    private $value_encoders = [];

    public function __construct(
        ActionResultContainerInterface $action_result_container,
        ValueEncoderInterface ...$value_encoders
    )
    {
        $this->setActionResultContainer($action_result_container);
        $this->addValueEncoder(...$value_encoders);
    }

    public function getActionResultContainer(): ActionResultContainerInterface
    {
        return $this->action_result_container;
    }

    public function &setActionResultContainer(
        ActionResultContainerInterface $action_result_container
    ): ActionResultEncoderInterface
    {
        $this->action_result_container = $action_result_container;

        return $this;
    }

    public function getEncodeOnExit(): bool
    {
        return $this->encode_on_exit;
    }

    public function &setEncodeOnExit(bool $value = true): ActionResultEncoderInterface
    {
        $this->encode_on_exit = $value;

        return $this;
    }

    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    public function &setLogger(LoggerInterface $logger = null): ActionResultEncoderInterface
    {
        $this->logger = $logger;

        return $this;
    }

    public function getValueEncoders(): array
    {
        return $this->value_encoders;
    }

    public function &addValueEncoder(ValueEncoderInterface ...$value_encoders): ActionResultEncoderInterface
    {
        $this->value_encoders = array_merge($this->value_encoders, $value_encoders);

        return $this;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface
    {
        if (!$this->getEncodeOnExit()) {
            throw new LogicException('Action result can be encoded after handling only.');
        }

        return $this->tryToEncodeValue(
            $handler->handle($request),
            $this->action_result_container
        );
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ): ResponseInterface
    {
        if (!$this->getEncodeOnExit()) {
            $response = $this->tryToEncodeValue($response, $this->action_result_container);
        }

        if ($next) {
            $response = $next($request, $response);
        }

        if ($this->getEncodeOnExit()) {
            $response = $this->tryToEncodeValue($response, $this->action_result_container);
        }

        return $response;
    }

    private function tryToEncodeValue(
        ResponseInterface $response,
        ActionResultContainerInterface $action_result_container
    )
    {
        if ($action_result_container->hasValue()) {
            $response = $this->encode($response, $this->action_result_container->getValue());
        } else if ($this->getLogger()) {
            $this->getLogger()->error('Action result not found in value container.');
        }

        return $response;
    }

    public function encode(ResponseInterface $response, $value): ResponseInterface
    {
        foreach ($this->value_encoders as $value_encoder) {
            if ($value_encoder->shouldEncode($value)) {
                return $value_encoder->encode($response, $this, $value);
            }
        }

        throw new RuntimeException(
            sprintf('No matching encoder for value of %s type found.', $this->getValueType($value))
        );
    }

    private function getValueType($value): string
    {
        return is_object($value) ? get_class($value) : gettype($value);
    }
}
