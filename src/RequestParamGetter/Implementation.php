<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller\RequestParamGetter;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @package ActiveCollab\Controller\RequestParamGetter
 */
trait Implementation
{
    /**
     * {@inheritdoc}
     */
    public function getParsedBodyParam(ServerRequestInterface $request, $param_name, $default = null)
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
     * {@inheritdoc}
     */
    public function getCookieParam(ServerRequestInterface $request, $param_name, $default = null)
    {
        return array_key_exists($param_name, $request->getCookieParams()) ? $request->getCookieParams()[$param_name] : $default;
    }

    /**\
     * {@inheritdoc}
     */
    public function getQueryParam(ServerRequestInterface $request, $param_name, $default = null)
    {
        return array_key_exists($param_name, $request->getQueryParams()) ? $request->getQueryParams()[$param_name] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getServerParam(ServerRequestInterface $request, $param_name, $default = null)
    {
        return array_key_exists($param_name, $request->getServerParams()) ? $request->getServerParams()[$param_name] : $default;
    }
}
