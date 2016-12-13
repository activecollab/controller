<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultEncoder;

use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\ValueEncoderInterface;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class ActionResultEncoder implements ActionResultEncoderInterface
{
    private $action_result_key;

    private $encode_on_exit = false;

    /**
     * @var ValueEncoderInterface[]
     */
    private $value_encoders;

    public function __construct(string $action_result_key = 'action_result', ValueEncoderInterface ...$value_encoders)
    {
        if (empty($action_result_key)) {
            throw new InvalidArgumentException('Request attribute name is required.');
        }

        $this->action_result_key = $action_result_key;
        $this->value_encoders = $value_encoders;
    }

    public function getActionResultKey(): string
    {
        return $this->action_result_key;
    }

    public function &setActionResultKey(string $action_result_key): ActionResultEncoderInterface
    {
        $this->action_result_key = $action_result_key;

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

    public function getValueEncoders(): array
    {
        return $this->value_encoders;
    }

    public function &addValueEncoder(ValueEncoderInterface $value_encoder): ActionResultEncoderInterface
    {
        $this->value_encoders[] = $value_encoder;

        return $this;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null): ResponseInterface
    {
        if (!$this->getEncodeOnExit()) {
            $response = $this->encodeToResponse($request, $response, $this->getActionResultKey());
        }

        if ($next) {
            $response = $next($request, $response);
        }

        if ($this->getEncodeOnExit()) {
            $response = $this->encodeToResponse($request, $response, $this->getActionResultKey());
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

        throw new RuntimeException("No matching encoder for value of {$this->getValueType($value)} type found.");
    }

    private function encodeToResponse(ServerRequestInterface $request, ResponseInterface $response, string $action_result_key): ResponseInterface
    {
        if (!array_key_exists($action_result_key, $request->getAttributes())) {
            throw new RuntimeException("Request attribute '{$action_result_key}' not found.");
        }

        return $this->encode($response, $request->getAttribute($action_result_key));
    }

    private function getValueType($value): string
    {
        return is_object($value) ? get_class($value) : gettype($value);
    }
}
