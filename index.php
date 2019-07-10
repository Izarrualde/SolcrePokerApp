<?php

use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;
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
use Solcre\lmsuy\Entity\UserEntity;
use Solcre\lmsuy\MySQL\ConnectLmsuy_db;
use Solcre\lmsuy\Service\UserService;

require __DIR__ . '/vendor/autoload.php';

date_default_timezone_set('America/Montevideo');

// Create Container using PHP-DI
$container = new Container();

// Set container to create App with on AppFactory
AppFactory::setContainer($container);

// Instantiate App
$app = AppFactory::create();

// Add error middleware

$responseFactory = $app->getResponseFactory();
$errorMiddleware = new ErrorMiddleware($app->getCallableResolver(), $responseFactory, true, true, true);
$app->add($errorMiddleware);

// Get container
$container = $app->getContainer();

$container->set(
    'settings',
    function ($container): Array {
        $set = include __DIR__ . '/settings.php';
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

// Add route
$app->get('/', 'SessionController:listAll');
$app->get('/sessions/{idSession:[0-9]+}', 'SessionController:list');
$app->get('/sessions', 'SessionController:listAll');
$app->post('/sessions', 'SessionController:add');
$app->get('/sessions/form', 'SessionController:form');
$app->get('/sessions/{idSession}/remove', 'SessionController:delete');
$app->post('/sessions/{idSession}/update', 'SessionController:update');
$app->get('/sessions/{idSession}/calculate', 'SessionController:CalculatePoints');


$app->get('/sessions/{idSession}/expenses', 'ExpensesSessionController:listAll');
$app->get('/sessions/{idSession}/expenses/{idExpenditure}/update', 'ExpensesSessionController:list');
$app->get('/sessions/{idSession}/expenses/form', 'ExpensesSessionController:form');
$app->post('/sessions/{idSession}/expenses/{idExpenditure}/update', 'ExpensesSessionController:update');
$app->post('/sessions/{idSession}/expenses', 'ExpensesSessionController:add');

$app->get('/sessions/{idSession}/expenses/{idExpenditure}/remove', 'ExpensesSessionController:delete');


$app->get('/sessions/{idSession}/buyins/', 'BuyinSessionController:listAll');
$app->get('/sessions/{idSession}/buyins/{idbuyin}', 'BuyinSessionController:list');
$app->post('/sessions/{idSession}/buyins', 'BuyinSessionController:add');
$app->get('/{idSession}/buyins/form', 'BuyinSessionController:form');
$app->get('/{idSession}/buyins/{idbuyin}/remove', 'BuyinSessionController:delete');
$app->post('/buyins/{idBuyin}/update', 'BuyinSessionController:update');



$app->get('/sessions/{idSession}/comissions/', 'ComissionSessionController:listAll');
$app->get('/sessions/{idSession}/comissions/{idcomission}', 'ComissionSessionController:list');

$app->post('/sessions/{idSession}/comissions', 'ComissionSessionController:add');
$app->get('/{idSession}/comissions/form', 'ComissionSessionController:form');
$app->get('/{idSession}/comissions/{idcomission}/remove', 'ComissionSessionController:delete');
$app->post('/comissions/{idcomission}/update', 'ComissionSessionController:update');

$app->get('/sessions/{idSession}/tips/', 'TipSessionController:listAll');
$app->get('/sessions/{idSession}/tips/dealerTip/{idDealerTip}', 'TipSessionController:list');

$app->get('/sessions/{idSession}/tips/serviceTip/{idServiceTip}', 'TipSessionController:list');
$app->post('/sessions/{idSession}/tips', 'TipSessionController:add');
$app->get('/{idSession}/tips/form', 'TipSessionController:form');
$app->get('/tips/dealertip/{idDealerTip}/remove', 'TipSessionController:delete');
$app->get('/tips/servicetip/{idServiceTip}/remove', 'TipSessionController:delete');
$app->post('/tips/dealertip/{idDealerTip}/update', 'TipSessionController:update');
$app->post('/tips/servicetip/{idServiceTip}/update', 'TipSessionController:update');

$app->get('/sessions/{idSession}/usersSession/', 'UserSessionController:listAll');
$app->get('/sessions/{idSession}/usersSession/{idusersession}', 'UserSessionController:list');
$app->post('/sessions/{idSession}/usersSession', 'UserSessionController:add');
$app->get('/{idSession}/usersSession/form', 'UserSessionController:form');
$app->get('/{idSession}/usersSession/{idusersession}/remove', 'UserSessionController:delete');
$app->post('/usersSession/{idusersession}/update', 'UserSessionController:update');
$app->get('/userssession/{idusersession}/formclose', 'UserSessionController:formClose');
$app->post('/userssession/{idusersession}/close', 'UserSessionController:close');

$app->get('/users/', 'UserController:listAll');
$app->get('/users/{iduser}', 'UserController:list');
$app->post('/users/', 'UserController:add');
$app->get('/form', 'UserController:form');
$app->get('/users/{iduser}/remove', 'UserController:delete');
$app->post('/users/{iduser}/update', 'UserController:update');

$app->run();
