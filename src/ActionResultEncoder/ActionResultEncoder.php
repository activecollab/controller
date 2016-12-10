<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\ActionResultEncoder;

use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\ValueEncoderInterface;

class ActionResultEncoder implements ActionResultEncoderInterface
{
    private $request_attribute_name;

    /**
     * @var ValueEncoderInterface[]
     */
    private $value_encoders;

    public function __construct(string $request_attribute_name = 'action_result', ValueEncoderInterface ...$value_encoders)
    {
        if (empty($request_attribute_name)) {
            throw new LogicException('Request attribute name is required.');
        }

        $this->request_attribute_name = $request_attribute_name;
        $this->value_encoders = $value_encoders;
    }

    public function &addValueEncoder(ValueEncoderInterface $value_encoder): ActionResultEncoderInterface
    {
        $this->value_encoders[] = $value_encoder;

        return $this;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null): ResponseInterface
    {
        $action_result = $request->getAttribute($this->request_attribute_name);

        foreach ($this->value_encoders as $value_encoder) {
            if ($value_encoder->shouldEncode($value_encoder)) {
                $response = $value_encoder->encode($response, $action_result);
                break;
            }
        }

        if ($next) {
            $response = $next($request, $response);
        }

        return $response;
    }
}
