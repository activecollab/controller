<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller\Response;

use ActiveCollab\TemplateEngine\TemplateEngineInterface;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;

/**
 * @package ActiveCollab\Controller\Response
 */
interface ViewResponseInterface extends ResponseInterface
{
    /**
     * Render a template.
     *
     * $data cannot contain template as a key
     *
     * throws RuntimeException if $templatePath . $template does not exist
     *
     * @param  Psr7ResponseInterface $response
     * @param  string                $template
     * @param  array                 $data
     * @return Psr7ResponseInterface
     */
    public function render(Psr7ResponseInterface $response, $template, array $data = []);

    /**
     * Return template engine associated with this view response.
     *
     * @return TemplateEngineInterface
     */
    public function &getTemplateEngine();
}
