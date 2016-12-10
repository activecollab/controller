<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Response;

use ActiveCollab\TemplateEngine\TemplateEngineInterface;

class ViewResponse implements ViewResponseInterface
{
    private $template_engine;

    private $template;

    private $template_data;

    private $content_type = 'text/html';

    private $encoding = 'UTF-8';

    public function __construct(TemplateEngineInterface $template_engine, string $template, array $data = [], string $content_type = 'text/html', string $encoding = 'UTF-8')
    {
        $this->template_engine = $template_engine;
        $this->template = $template;
        $this->template_data = $data;
        $this->content_type = $content_type;
        $this->encoding = $encoding;
    }

    public function fetch(): string
    {
        return $this->template_engine->fetch($this->template, $this->template_data);
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
