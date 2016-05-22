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
class ViewResponse implements ViewResponseInterface
{
    /**
     * @var TemplateEngineInterface
     */
    private $template_engine;

    /**
     * @var string
     */
    private $template;

    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $content_type = 'text/html';

    /**
     * @var string
     */
    private $encoding = 'UTF-8';

    /**
     * @param TemplateEngineInterface $template_engine
     * @param string                  $template
     * @param array                   $template_data
     * @param string                  $content_type
     * @param string                  $encoding
     */
    public function __construct(TemplateEngineInterface &$template_engine, $template, array $template_data = [], $content_type = 'text/html', $encoding = 'UTF-8')
    {
        $this->template_engine = $template_engine;
        $this->template = $template;
        $this->data = $template_data;
        $this->content_type = $content_type;
        $this->encoding = $encoding;
    }

    /**
     * {@inheritdoc}
     */
    public function render(Psr7ResponseInterface $response)
    {
        if ($this->getContentType() && $this->getEncoding()) {
            $response = $response->withHeader('Content-Type', $this->getContentType() . ';charset=' . $this->getEncoding());
        }

        $response->getBody()->write($this->template_engine->fetch($this->template, $this->data));

        return $response;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->content_type;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }
}
