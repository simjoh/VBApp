<?php


use App\common\Rest\Client\RusaTimeRestClient;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Middleware\ErrorMiddleware;
use Slim\Views\PhpRenderer;

return [
    'settings' => function () {
        return require __DIR__ . '/settings.php';
    },

    App::class => function (ContainerInterface $container) {
        AppFactory::setContainer($container);

        return AppFactory::create();
    },

    RouteCollectorInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getRouteCollector();
    },



    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getResponseFactory();
    },

    ErrorMiddleware::class => function (ContainerInterface $container) {
        $app = $container->get(App::class);
        $settings = $container->get('settings')['error'];

        return new ErrorMiddleware(
            $app->getCallableResolver(),
            $app->getResponseFactory(),
            (bool)$settings['display_error_details'],
            (bool)$settings['log_errors'],
            (bool)$settings['log_error_details']
        );
    },
    PDO::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['db'];

        $host = $settings['host'];
        $dbname = $settings['database'];
        $username = $settings['username'];
        $password = $settings['password'];
        $charset = $settings['charset'];
        $flags = $settings['flags'];
        $port = $settings['port'];
        $dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=$charset";



        return new PDO($dsn, $username, $password);
    },

    PhpRenderer::class => function (ContainerInterface $container) {
        return new PhpRenderer($container->get('settings')['view']['path']);
    },


    RusaTimeRestClient::class => function(ContainerInterface $container){
             return new RusaTimeRestClient($container->get('settings')['rusaurl']);

    },

    \App\Domain\Model\Club\Rest\ClubAssembly::class => function(ContainerInterface $container) {
        return new \App\Domain\Model\Club\Rest\ClubAssembly(
            $container->get(\App\Domain\Permission\PermissionRepository::class),
            $container,
            $container->get(PDO::class)
        );
    },

    \App\Domain\Model\Organizer\Rest\OrganizerAssembly::class => function(ContainerInterface $container) {
        return new \App\Domain\Model\Organizer\Rest\OrganizerAssembly(
            $container->get(\App\Domain\Permission\PermissionRepository::class),
            $container
        );
    },

    \App\Domain\Model\Organizer\Service\OrganizerService::class => function(ContainerInterface $container) {
        return new \App\Domain\Model\Organizer\Service\OrganizerService(
            $container,
            $container->get(\App\Domain\Model\Organizer\Repository\OrganizerRepository::class),
            $container->get(\App\Domain\Permission\PermissionRepository::class),
            $container->get(\App\Domain\Model\Organizer\Rest\OrganizerAssembly::class)
        );
    },

    \App\Domain\Model\Organizer\Repository\OrganizerRepository::class => function(ContainerInterface $container) {
        return new \App\Domain\Model\Organizer\Repository\OrganizerRepository(
            $container->get(PDO::class)
        );
    },

    \App\Action\Organizer\OrganizerAction::class => function(ContainerInterface $container) {
        return new \App\Action\Organizer\OrganizerAction(
            $container,
            $container->get(\App\Domain\Model\Organizer\Service\OrganizerService::class)
        );
    }





];
