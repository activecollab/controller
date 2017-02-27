<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test\Encoder;

use ActiveCollab\Controller\ActionResult\FileDownloadResult\FileDownloadResult;
use ActiveCollab\Controller\ActionResult\StatusResult\StatusResult;
use ActiveCollab\Controller\ActionResultEncoder\ActionResultEncoder;
use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\FileDownloadEncoder;
use ActiveCollab\Controller\Test\Base\TestCase;
use ActiveCollab\Controller\Test\Fixtures\ActionResultInContainer;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;

class FileDownloadEncoderTest extends TestCase
{
    private $container;

    /**
     * @var ActionResultInContainer
     */
    private $action_result_container;

    public function setUp()
    {
        parent::setUp();

        $this->container = new Container();
        $this->action_result_container = new ActionResultInContainer($this->container);
    }

    public function testShouldEncode()
    {
        $this->assertFalse((new FileDownloadEncoder())->shouldEncode([1, 2, 3]));
        $this->assertFalse((new FileDownloadEncoder())->shouldEncode(new StatusResult(200)));
        $this->assertTrue((new FileDownloadEncoder())->shouldEncode(new FileDownloadResult(__FILE__, 'text/php')));
    }

    public function testForcedFileDownloadEncoder()
    {
        $response = $this->createResponse()->withHeader('X-Test', 'yes');

        $file_download = new FileDownloadResult(__FILE__, 'text/php');
        $file_download->addCustomHeader('x-autoupgrade-package-hash', md5_file(__FILE__));

        $response = (new FileDownloadEncoder())->encode($response, new ActionResultEncoder($this->action_result_container), $file_download);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertContains('yes', $response->getHeaderLine('X-Test'));

        $headers = $response->getHeaders();

        $this->assertArrayHasKey('Content-Type', $headers);
        $this->assertSame('application/force-download', $headers['Content-Type'][0]);

        $this->assertArrayHasKey('Content-Description', $headers);
        $this->assertSame('File Transfer', $headers['Content-Description'][0]);

        $this->assertArrayHasKey('Content-Transfer-Encoding', $headers);
        $this->assertSame('binary', $headers['Content-Transfer-Encoding'][0]);

        $this->assertArrayHasKey('Content-Disposition', $headers);
        $this->assertContains('attachment', $headers['Content-Disposition'][0]);
        $this->assertContains(basename(__FILE__), $headers['Content-Disposition'][0]);

        $this->assertArrayHasKey('Content-Length', $headers);
        $this->assertSame(filesize(__FILE__), $headers['Content-Length'][0]);

        $this->assertArrayHasKey('Expires', $headers);
        $this->assertSame('0', $headers['Expires'][0]);

        $this->assertArrayHasKey('Cache-Control', $headers);
        $this->assertSame('must-revalidate, post-check=0, pre-check=0', $headers['Cache-Control'][0]);

        $this->assertArrayHasKey('Pragma', $headers);
        $this->assertSame('public', $headers['Pragma'][0]);

        $this->assertArrayHasKey('x-autoupgrade-package-hash', $headers);
        $this->assertSame(md5_file(__FILE__), $headers['x-autoupgrade-package-hash'][0]);
    }

    public function testInlineFileDownloadEncoder()
    {
        $response = $this->createResponse()->withHeader('X-Test', 'yes');

        $file_download = new FileDownloadResult(__FILE__, 'text/php', true);

        $response = (new FileDownloadEncoder())->encode($response, new ActionResultEncoder($this->action_result_container), $file_download);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertContains('yes', $response->getHeaderLine('X-Test'));

        $headers = $response->getHeaders();

        $this->assertArrayHasKey('Content-Type', $headers);
        $this->assertSame('text/php', $headers['Content-Type'][0]);

        $this->assertArrayHasKey('Content-Description', $headers);
        $this->assertSame('Binary', $headers['Content-Description'][0]);

        $this->assertArrayHasKey('Content-Transfer-Encoding', $headers);
        $this->assertSame('binary', $headers['Content-Transfer-Encoding'][0]);

        $this->assertArrayHasKey('Content-Disposition', $headers);
        $this->assertContains('inline', $headers['Content-Disposition'][0]);
        $this->assertContains(basename(__FILE__), $headers['Content-Disposition'][0]);

        $this->assertArrayHasKey('Content-Length', $headers);
        $this->assertSame(filesize(__FILE__), $headers['Content-Length'][0]);

        $this->assertArrayHasKey('Expires', $headers);
        $this->assertSame('0', $headers['Expires'][0]);

        $this->assertArrayHasKey('Cache-Control', $headers);
        $this->assertSame('must-revalidate, post-check=0, pre-check=0', $headers['Cache-Control'][0]);

        $this->assertArrayHasKey('Pragma', $headers);
        $this->assertSame('public', $headers['Pragma'][0]);
    }
}
