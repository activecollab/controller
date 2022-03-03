<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test;

use ActiveCollab\Controller\ActionResult\FileDownloadResult\FileDownloadResult;
use ActiveCollab\Controller\Test\Base\TestCase;
use InvalidArgumentException;

class FileDownloadResultTest extends TestCase
{
    public function testExceptionOnEmptyFilePath()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("File path is required.");

        new FileDownloadResult('', 'application/octet-stream');
    }

    public function testExceptionOnMissingFile()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Download file not found.");

        $this->assertFalse(file_exists('/unknwon file'));
        new FileDownloadResult('/unknwon file', 'application/octet-stream');
    }

    public function testDefaultContentType()
    {
        $this->assertSame('application/octet-stream', (new FileDownloadResult(__FILE__, ''))->getContentType());
        $this->assertSame('text/php', (new FileDownloadResult(__FILE__, 'text/php'))->getContentType());
    }

    public function testDefaultFilename()
    {
        $this->assertSame(basename(__FILE__), (new FileDownloadResult(__FILE__, ''))->getFileName());
        $this->assertSame('x.y', (new FileDownloadResult(__FILE__, '', true, 'x.y'))->getFileName());
    }
}
