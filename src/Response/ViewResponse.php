<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Response;

use ActiveCollab\TemplateEngine\TemplateEngineInterface;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;

class ViewResponse implements ViewResponseInterface
{
    private $template_engine;

    private $template;

    private $data;

    private $content_type = 'text/html';

    private $encoding = 'UTF-8';

    public function __construct(TemplateEngineInterface &$template_engine, string $template, array $template_data = [], string $content_type = 'text/html', string $encoding = 'UTF-8')
    {
        $this->template_engine = $template_engine;
        $this->template = $template;
        $this->data = $template_data;
        $this->content_type = $content_type;
        $this->encoding = $encoding;
    }

    public function render(Psr7ResponseInterface $response): Psr7ResponseInterface
    {
        if ($this->getContentType() && $this->getEncoding()) {
            $response = $response->withHeader('Content-Type', $this->getContentType() . ';charset=' . $this->getEncoding());
        }

        $response->getBody()->write($this->template_engine->fetch($this->template, $this->data));

        return $response;
    }

    public function getContentType(): string
    {
        return $this->content_type;
    }

    public function getEncoding(): string
    {
        return $this->encoding;
    }
}
