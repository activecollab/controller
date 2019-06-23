<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test\Base;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Environment as SlimEnvironment;
use Slim\Http\Request as SlimRequest;
use Slim\Http\Response as SlimResponse;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function createRequest(string $method = 'GET', string $path = '/', array $query_params = [], array $payload = []): ServerRequestInterface
    {
        $environment_user_data = [
            'REQUEST_METHOD' => $method,
            'REQUEST_URI' => '/' . trim($path, '/'),
        ];

        if (!empty($query_params)) {
            $environment_user_data['QUERY_STRING'] = http_build_query($query_params);
        }

        $environment = SlimEnvironment::mock($environment_user_data);

        $request = SlimRequest::createFromEnvironment($environment)
            ->withParsedBody($payload);

        return $request;
    }

    protected function createResponse(): ResponseInterface
    {
        return new SlimResponse();
    }
}
