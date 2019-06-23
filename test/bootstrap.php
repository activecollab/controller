<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

defined('BASE_PATH') || define('BASE_PATH', realpath(__DIR__));

date_default_timezone_set('GMT');

require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/src/Base/TestCase.php';
