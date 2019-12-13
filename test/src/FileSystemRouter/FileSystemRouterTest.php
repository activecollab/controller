<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test\FileSystemRouter;

use ActiveCollab\Controller\FileSystemRouter\FileSystemRouter;
use ActiveCollab\Controller\Test\Base\TestCase;

class FileSystemRouterTest extends TestCase
{
    private $blog_example_dir;

    protected function setUp()
    {
        parent::setUp();

        $this->blog_example_dir = dirname(dirname(__DIR__)) . '/fixtures/blog_example';
        $this->assertDirectoryExists($this->blog_example_dir);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Path "not a directory" is not a directory.
     */
    public function testWillThrowExceptionOnMissingDir()
    {
        (new FileSystemRouter())->scan('not a directory');
    }

    public function testWillWalkRecursivelyThrougDir(): void
    {
        $routes = (new FileSystemRouter())->scan($this->blog_example_dir);

        $this->assertGreaterThan(5, count($routes));
    }

//    public function testWillDiscoverIndexFiles(): void
//    {
//    }
//
//    public function testWillDiscoverParametrizedDirs(): void
//    {
//    }
//
//    public function testWillDiscoverParametrizedFiles(): void
//    {
//    }
}
