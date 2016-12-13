<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultEncoder;

use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\ValueEncoderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ActionResultEncoderInterface
{
    public function getActionResultkey(): string;

    public function &setActionResultkey(string $action_result_key): ActionResultEncoderInterface;

    public function getEncodeOnExit(): bool;

    public function &setEncodeOnExit(bool $value = true): ActionResultEncoderInterface;

    public function getValueEncoders(): array;

    public function &addValueEncoder(ValueEncoderInterface $value_encoder): ActionResultEncoderInterface;

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null): ResponseInterface;

    public function encode(ResponseInterface $response, $value): ResponseInterface;
}
