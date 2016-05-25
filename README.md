# Controller

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
