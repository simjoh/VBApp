<?php

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;
use Selective\BasePath\BasePathMiddleware;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return function (App $app) {
    // Add CORS middleware first
    $app->add(\App\Middleware\CorsMiddleware::class);

    // Add UserContext cleanup middleware (should be one of the first to ensure cleanup)
    $app->add(\App\Middleware\UserContextCleanupMiddleware::class);

    // Parse json, form data and xml
    $app->addBodyParsingMiddleware();

    // Add the Slim built-in routing middleware
    $app->addRoutingMiddleware();

    // Configure BasePathMiddleware to handle /api/ base path
    $app->add(new BasePathMiddleware($app, '/api'));

    // Add UserContext middleware (after BasePathMiddleware but before routes)
    $app->add(\App\Middleware\UserContextMiddleware::class);

//    // Catch exceptions and errors
//    $app->add(ErrorMiddleware::class);
//
//    // Define Custom Error Handler
//
//    $errorMiddleware = $app->addErrorMiddleware(true, true, true);
//
//
//
//    $errorHandler = $errorMiddleware->getDefaultErrorHandler();
//    $errorHandler->forceContentType('application/json');

    // Define Custom Error Handler
    $customErrorHandler = function (
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails,
        ?LoggerInterface $logger = null
    ) use ($app) {
//        $logger->error($exception->getMessage());
//
//        $payload = ['error' => $exception->getMessage()];

        $error = new \App\common\Exceptions\BrevetExceptionrepresentation();
        $error->setMessage($exception->getMessage());
       // $error->setCode($exception->getFile() . $exception->getLine());
        $error->setCode($exception->getCode());

        $response = $app->getResponseFactory()->createResponse();
        $response->getBody()->write(
            json_encode($error, JSON_UNESCAPED_UNICODE)
        );

        return $response->withStatus(500);
    };

    $twig = Twig::create('../templates', ['cache' => false]);

// Add Twig-View Middleware
    $app->add(TwigMiddleware::create($app, $twig));

// Add Error Middleware
    $errorMiddleware = $app->addErrorMiddleware(true, true, true);
    $errorMiddleware->setDefaultErrorHandler($customErrorHandler);
};
