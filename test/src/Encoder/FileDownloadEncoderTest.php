<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test\Encoder;

use ActiveCollab\Controller\ActionResultEncoder\ActionResultEncoder;
use ActiveCollab\Controller\ActionResultEncoder\ValueEncoder\FileDownloadEncoder;
use ActiveCollab\Controller\ActionResult\FileDownloadResult;
use ActiveCollab\Controller\Response\StatusResponse\OkStatusResult;
use ActiveCollab\Controller\Test\Base\TestCase;
use Psr\Http\Message\ResponseInterface;

class FileDownloadEncoderTest extends TestCase
{
    public function testShouldEncode()
    {
        $this->assertFalse((new FileDownloadEncoder())->shouldEncode([1, 2, 3]));
        $this->assertFalse((new FileDownloadEncoder())->shouldEncode(new OkStatusResult()));
        $this->assertTrue((new FileDownloadEncoder())->shouldEncode(new FileDownloadResult(__FILE__, 'text/php')));
    }

    public function testForcedFileDownloadEncoder()
    {
        $response = $this->createResponse()->withHeader('X-Test', 'yes');

        $file_download = new FileDownloadResult(__FILE__, 'text/php');

        $response = (new FileDownloadEncoder())->encode($response, new ActionResultEncoder(), $file_download);
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
    }

    public function testInlineFileDownloadEncoder()
    {
        $response = $this->createResponse()->withHeader('X-Test', 'yes');

        $file_download = new FileDownloadResult(__FILE__, 'text/php', true);

        $response = (new FileDownloadEncoder())->encode($response, new ActionResultEncoder(), $file_download);
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
