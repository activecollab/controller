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
        $uri = $this->getUri($path);

        return (new ServerRequestFactory())->createServerRequest(
            $method,
            $uri,
            [
                'REQUEST_METHOD' => $method,
                'REQUEST_URI' => $uri,
            ]
        )->withParsedBody($payload)->withQueryParams($query_params);
    }

    private function getUri(string $path): string
    {
        return '/' . trim($path, '/');
    }

    protected function createResponse(): ResponseInterface
    {
        return (new ResponseFactory())->createResponse();
    }
}
