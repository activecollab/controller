<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test\Base;

use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function createRequest(
        string $method = 'GET',
        string $path = '/',
        array $query_params = [],
        array $payload = []
    ): ServerRequestInterface
    {
        return (new ServerRequestFactory())->createServerRequest(
            $method,
            $this->getUri($path, $query_params)
        )->withParsedBody($payload);
    }

    private function getUri(
        string $path,
        array $query_params
    ): string
    {
        $uri = '/' . trim($path, '/');

        if (!empty($query_params)) {
            $uri .= '?' . http_build_query($query_params);
        }

        return $uri;
    }

    protected function createResponse(): ResponseInterface
    {
        return (new ResponseFactory())->createResponse();
    }
}
