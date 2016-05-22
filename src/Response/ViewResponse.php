<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Controller\Response;

use ActiveCollab\TemplateEngine\TemplateEngineInterface;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;

/**
 * @package ActiveCollab\Controller\Response
 */
abstract class ViewResponse implements ViewResponseInterface
{
    /**
     * @var TemplateEngineInterface
     */
    private $template_engine;

    /**
     * @param TemplateEngineInterface $template_engine
     */
    public function __construct(TemplateEngineInterface &$template_engine)
    {
        $this->template_engine = $template_engine;
    }

    /**
     * {@inheritdoc}
     */
    public function &getTemplateEngine()
    {
        return $this->template_engine;
    }

    /**
     * {@inheritdoc}
     */
    public function render(Psr7ResponseInterface $response, $template, array $data = [])
    {
        $response->getBody()->write($this->fetch($template, $data));

        return $response;
    }
}
