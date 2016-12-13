# Controller

[![Build Status](https://travis-ci.org/activecollab/controller.svg?branch=v1.0)](https://travis-ci.org/activecollab/controller)

Supported action responses:

1. `\ActiveCollab\Controller\Response\FileDownloadResponse` - streams a file download.
1. `\ActiveCollab\Controller\Response\StatusResponse` - returns a HTTP status, without response body.
1. `\ActiveCollab\Controller\Response\ViewResponse` - Renders a particular view.

When within a controller action, use these methods to get individual request parameters:

1. `getParsedBodyParam()`
2. `getCookieParam()`
3. `getQueryParam()`
4. `getServerParam()`

All of these methods accept three parameters:

1. `$request` (`\Psr\Http\Message\ServerRequestInterface` instance)
2. `$param_name` (string)
3. `$default` (mixed, `NULL` by default)

## Configuration

Controllers can override protected `configure()` method to do additional setup after controller construction. This method is separated from constructor, so developer does not need to inherti and manage complicated controller constructor.
 
```php
<?php

namespace App;

use ActiveCollab\Controller\Controller;

class TestController extends Controller
{
    public $is_configured = false;

    protected function configure(): void
    {
        $this->is_configured = true;
    }
}
```

## Exception Handling

When action fails due to an exception, system will return 500 HTTP error, with a message that does not expose any of the system details. 

This is done in such a way that new `RuntimeException` is constructed, with generic error message, and real exception is passed as `$previous` constructor argument of the new exception. If you have your system configured so exceptions are fully described when 500 errors are rendered (in debug mode for example), you'll be able to access original exception detials like that.

To change default exception message, call `setLogExceptionMessage()` controller method:

```php
$controller->setLogExceptionMessage('Something weird happened: {exception}');
```

If `$logger` is added to the controller (during construction or later on), all exceptions that actions throw will be logged with error level.
