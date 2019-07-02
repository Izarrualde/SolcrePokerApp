<?php

namespace Test;

use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Solcre\lmsuy\Controller\SessionController;
use Solcre\lmsuy\Controller\BuyinSessionController;
use Solcre\lmsuy\Controller\ComissionSessionController;
use Solcre\lmsuy\Controller\TipSessionController;
use Solcre\lmsuy\Controller\UserSessionController;
use Solcre\lmsuy\Controller\UserController;
use Solcre\lmsuy\Controller\ExpensesSessionController;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;

class AppWrapper 
{

  use ContainerAwareTrait;

  public function __construct(App $app) {
      // Get container
      $container = $app->getContainer();

      self::$container = $container;

      $container->set(
        'settings',
        function ($container): Array {
            $set = include __DIR__ . '/../settings.php';
            return $set;
        }
    );

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

    // Register component on container
    $container->set(
        'view',
        function ($container) {
            $settings = $container->get('settings');
            $view = new \Slim\Views\Twig('templates');
            $environment = $view->getEnvironment();
            $extension = $environment->getExtension(\Twig\Extension\CoreExtension::class);
            $extension->setNumberFormat(
                $settings['number_format']['decimals'],
                $settings['number_format']['decimal_separator'],
                $settings['number_format']['thousand_separator']
            );
            return $view;
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
	}
}