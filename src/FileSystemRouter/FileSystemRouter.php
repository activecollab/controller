<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\FileSystemRouter;

use DirectoryIterator;
use RuntimeException;

class FileSystemRouter
{
    public function scan(string $dir): array
    {
        if (!is_dir($dir)) {
            throw new RuntimeException(sprintf('Path "%s" is not a directory.', $dir));
        }

        foreach (new DirectoryIterator($dir) as $file) {
            if ($file->isFile()) {
//                print $file->getFilename() . "\n";
            }
        }

        return [];
    }
}
