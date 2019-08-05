<?php

namespace Test;

use Slim\App;

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
                $set = include __DIR__ . '/../app/settings.php';
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
	}
}