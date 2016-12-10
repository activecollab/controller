<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test\Encoder;

use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\ViewEncoder;
use ActiveCollab\Controller\Response\StatusResponse\OkStatusResponse;
use ActiveCollab\Controller\Response\ViewResponse;
use ActiveCollab\Controller\Test\Base\TestCase;
use ActiveCollab\TemplateEngine\TemplateEngine\PhpTemplateEngine;
use Psr\Http\Message\ResponseInterface;

class ViewEncoderTest extends TestCase
{
    private $templates_path;

    private $template_engine;

    public function setUp()
    {
        parent::setUp();

        $this->templates_path = dirname(__DIR__, 2) . '/templates';
        $this->assertDirectoryExists($this->templates_path);

        $this->template_engine = new PhpTemplateEngine($this->templates_path);
    }

    public function testShouldEncode()
    {
        $this->assertFalse((new ViewEncoder())->shouldEncode([1, 2, 3]));
        $this->assertFalse((new ViewEncoder())->shouldEncode(new OkStatusResponse()));
        $this->assertTrue((new ViewEncoder())->shouldEncode(new ViewResponse($this->template_engine, 'example.php')));
    }

    public function testEncodeView()
    {
        $response = $this->createResponse()->withHeader('X-Test', 'yes');

        $view = new ViewResponse($this->template_engine, 'example.php', [
            'first_name' => 'John Doe',
        ]);

        $response = (new ViewEncoder($this->template_engine))->encode($response, $view);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertContains('yes', $response->getHeaderLine('X-Test'));
        $this->assertContains('text/html', $response->getHeaderLine('Content-Type'));

        $response_body = (string) $response->getBody();

        $this->assertContains('<h1>Welcome John Doe</h1>', $response_body);
    }
}
