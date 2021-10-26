<?php

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;
use Selective\BasePath\BasePathMiddleware;

return function (App $app) {
    // Parse json, form data and xml
    $app->addBodyParsingMiddleware();

    // Add the Slim built-in routing middleware
    $app->addRoutingMiddleware();

    $app->add(BasePathMiddleware::class);


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

        $response = $app->getResponseFactory()->createResponse();
        $response->getBody()->write(
            json_encode($exception->getMessage(), JSON_UNESCAPED_UNICODE)
        );

        return $response->withStatus(500);
    };

// Add Error Middleware
    $errorMiddleware = $app->addErrorMiddleware(true, true, true);
    $errorMiddleware->setDefaultErrorHandler($customErrorHandler);
};
