<?php

declare(strict_types=1);

use DI\Container;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Log\LoggerInterface;
use Slim\App;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\Controller\SessionController;
use Solcre\lmsuy\Controller\BuyinSessionController;
use Solcre\lmsuy\Controller\ComissionSessionController;
use Solcre\lmsuy\Controller\TipSessionController;
use Solcre\lmsuy\Controller\UserSessionController;
use Solcre\lmsuy\Controller\UserController;
use Solcre\lmsuy\Controller\ExpensesSessionController;
use Solcre\lmsuy\Controller\RakebackController;

return function (App $app) {
    /** @var Container $container */
    $container = $app->getContainer();
/*
    $container->set(LoggerInterface::class, function (Container $c) {
        $settings = $c->get('settings');

        $loggerSettings = $settings['logger'];
        $logger = new Logger($loggerSettings['name']);

        $processor = new UidProcessor();
        $logger->pushProcessor($processor);

        $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
        $logger->pushHandler($handler);

        return $logger;
    });
*/
    $container->set(
        EntityManager::class,
        function ($container): EntityManager {
            $config = Setup::createAnnotationMetadataConfiguration(
                $container->get('settings')['doctrine']['metadata_dirs'],
                $container->get('settings')['doctrine']['dev_mode']
            );

            $config->setMetadataDriverImpl(
                new AnnotationDriver(
                    new AnnotationReader,
                    $container->get('settings')['doctrine']['metadata_dirs']
                )
            );

            $config->setMetadataCacheImpl(
                new FilesystemCache(
                    $container->get('settings')['doctrine']['cache_dir']
                )
            );
            return EntityManager::create(
                $container->get('settings')['doctrine']['connection'],
                $config
            );
        }
    );

    $container->set(
        'SessionController',
        function ($c) {
            $view = $c->get("view"); // retrieve the 'view' from the container
            $em   = $c->get(EntityManager::class);
            return new SessionController($view, $em);
        }
    );

    $container->set(
        'BuyinSessionController',
        function ($c) {
            $view = $c->get("view"); // retrieve the 'view' from the container
            $em = $c->get(EntityManager::class);
            return new BuyinSessionController($view, $em);
        }
    );

    $container->set(
        'ComissionSessionController',
        function ($c) {
            $view = $c->get("view"); // retrieve the 'view' from the container
            $em = $c->get(EntityManager::class);
            return new ComissionSessionController($view, $em);
        }
    );

    $container->set(
        'TipSessionController',
        function ($c) {
            $view = $c->get("view"); // retrieve the 'view' from the container
            $em = $c->get(EntityManager::class);
            return new TipSessionController($view, $em);
        }
    );

    $container->set(
        'UserController',
        function ($c) {
            $view = $c->get("view"); // retrieve the 'view' from the container
            $em = $c->get(EntityManager::class);
            return new UserController($view, $em);
        }
    );

    $container->set(
        'UserSessionController',
        function ($c) {
            $view = $c->get("view"); // retrieve the 'view' from the container
            $em = $c->get(EntityManager::class);
            return new UserSessionController($view, $em);
        }
    );

    $container->set(
        'ExpensesSessionController',
        function ($c) {
            $view = $c->get("view"); // retrieve the 'view' from the container
            $em = $c->get(EntityManager::class);
            return new ExpensesSessionController($view, $em);
        }
    );

    $container->set(
        'RakebackController',
        function ($c) {
            $view = $c->get("view"); // retrieve the 'view' from the container
            return new RakebackController($view);
        }
    );
};
