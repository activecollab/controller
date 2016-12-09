<?php

declare(strict_types=1);

namespace ActiveCollab\Controller\Test;

use ActiveCollab\Controller\Response\FileDownloadResponse;
use ActiveCollab\Controller\Test\Base\TestCase;

class FileDownloadResponseTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage File path is required.
     */
    public function testExceptionOnEmptyFilePath()
    {
        new FileDownloadResponse('', 'application/octet-stream');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Download file not found.
     */
    public function testExceptionOnMissingFile()
    {
        $this->assertFalse(file_exists('/unknwon file'));
        new FileDownloadResponse('/unknwon file', 'application/octet-stream');
    }

    public function testDefaultContentType()
    {
        $this->assertSame('application/octet-stream', (new FileDownloadResponse(__FILE__, ''))->getContentType());
        $this->assertSame('text/php', (new FileDownloadResponse(__FILE__, 'text/php'))->getContentType());
    }

    public function testDefaultFilename()
    {
        $this->assertSame(basename(__FILE__), (new FileDownloadResponse(__FILE__, ''))->getFileName());
        $this->assertSame('x.y', (new FileDownloadResponse(__FILE__, '', true, 'x.y'))->getFileName());
    }
}