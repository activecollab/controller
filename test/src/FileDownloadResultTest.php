<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test;

use ActiveCollab\Controller\ActionResult\FileDownloadResult;
use ActiveCollab\Controller\Test\Base\TestCase;

class FileDownloadResultTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage File path is required.
     */
    public function testExceptionOnEmptyFilePath()
    {
        new FileDownloadResult('', 'application/octet-stream');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Download file not found.
     */
    public function testExceptionOnMissingFile()
    {
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
