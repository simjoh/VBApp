<?php


use App\common\Rest\Client\RusaTimeRestClient;
use App\common\Service\EmailService;
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

    // Email service registration
    EmailService::class => function (ContainerInterface $container) {
        return new EmailService($container);
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

    \App\common\Rest\Client\LoppServiceOrganizerRestClient::class => function(ContainerInterface $container) {
        return new \App\common\Rest\Client\LoppServiceOrganizerRestClient($container->get('settings'));
    },

    \App\common\Rest\Client\LoppServiceClubRestClient::class => function(ContainerInterface $container) {
        return new \App\common\Rest\Client\LoppServiceClubRestClient($container->get('settings'));
    },

    \App\Domain\Model\Organizer\Service\OrganizerService::class => function(ContainerInterface $container) {
        return new \App\Domain\Model\Organizer\Service\OrganizerService(
            $container,
            $container->get(\App\Domain\Model\Organizer\Repository\OrganizerRepository::class),
            $container->get(\App\Domain\Permission\PermissionRepository::class),
            $container->get(\App\Domain\Model\Organizer\Rest\OrganizerAssembly::class),
            $container->get(\App\common\Rest\Client\LoppServiceOrganizerRestClient::class)
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
    },

    \App\Domain\Model\Club\ClubRepository::class => function(ContainerInterface $container) {
        return new \App\Domain\Model\Club\ClubRepository(
            $container->get(PDO::class)
        );
    },

    \App\Domain\Model\Club\Service\ClubService::class => function(ContainerInterface $container) {
        return new \App\Domain\Model\Club\Service\ClubService(
            $container,
            $container->get(\App\Domain\Model\Club\ClubRepository::class),
            $container->get(\App\Domain\Permission\PermissionRepository::class),
            $container->get(\App\Domain\Model\Club\Rest\ClubAssembly::class),
            $container->get(\App\common\Rest\Client\LoppServiceClubRestClient::class)
        );
    },

    \App\Action\Club\ClubAction::class => function(ContainerInterface $container) {
        return new \App\Action\Club\ClubAction(
            $container,
            $container->get(\App\Domain\Model\Club\Service\ClubService::class)
        );
    },

    \App\Action\Test\TestEmailAction::class => function(ContainerInterface $container) {
        return new \App\Action\Test\TestEmailAction(
            $container->get(EmailService::class),
            $container
        );
    },

    \App\common\Database\MigrationManager::class => function(ContainerInterface $container) {
        return new \App\common\Database\MigrationManager(
            $container->get(PDO::class)
        );
    },

    \App\Action\Migration\MigrationAction::class => function(ContainerInterface $container) {
        return new \App\Action\Migration\MigrationAction(
            $container->get(\App\common\Database\MigrationManager::class)
        );
    }

];
