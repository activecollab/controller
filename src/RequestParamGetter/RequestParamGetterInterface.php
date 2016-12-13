<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\RequestParamGetter;

use Psr\Http\Message\ServerRequestInterface;

interface RequestParamGetterInterface
{
    /**
     * Return a param from a parsed body.
     *
     * This method is NULL or object safe - it will check for body type, and do it's best to return a value without
     * breaking or throwing a warning.
     *
     * @param  ServerRequestInterface $request
     * @param  string                 $param_name
     * @param  mixed|null             $default
     * @return mixed|null
     */
    public function getParsedBodyParam(ServerRequestInterface $request, string $param_name, $default = null);

    /**
     * @param  ServerRequestInterface $request
     * @param  string                 $param_name
     * @param  mixed|null             $default
     * @return mixed
     */
    public function getCookieParam(ServerRequestInterface $request, string $param_name, $default = null);

    /**
     * @param  ServerRequestInterface $request
     * @param  string                 $param_name
     * @param  mixed|null             $default
     * @return mixed
     */
    public function getQueryParam(ServerRequestInterface $request, $param_name, $default = null);

    /**
     * @param  ServerRequestInterface $request
     * @param  string                 $param_name
     * @param  mixed|null             $default
     * @return mixed
     */
    public function getServerParam(ServerRequestInterface $request, string $param_name, $default = null);
}
