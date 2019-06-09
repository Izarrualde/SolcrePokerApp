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
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;
Use Solcre\lmsuy\Entity\UserEntity;
Use Solcre\lmsuy\MySQL\ConnectLmsuy_db;
use Solcre\lmsuy\Service\UserService;

require __DIR__ . '/vendor/autoload.php';

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

$container->set('settings', function ($container): Array {
	$set = require __DIR__ . '/settings.php';
	return $set;
});
$container->set(EntityManager::class, function ($container): EntityManager {
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
});

// Register component on container
$container->set('view', function ($container) {
    $view = new \Slim\Views\Twig('templates');

    // Instantiate and add Slim specific extension
    //$router = $container->get('router');
    //$uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    //$view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
    return $view;
});

$container->set('SessionController', function($c) {
    $view = $c->get("view"); // retrieve the 'view' from the container
    return new SessionController($view);
});
$container->set('BuyinSessionController', function($c) {
    $view = $c->get("view"); // retrieve the 'view' from the container
    return new BuyinSessionController($view);
});

$container->set('ComissionSessionController', function($c) {
    $view = $c->get("view"); // retrieve the 'view' from the container
    return new ComissionSessionController($view);
});

$container->set('TipSessionController', function($c) {
    $view = $c->get("view"); // retrieve the 'view' from the container
    return new TipSessionController($view);
});

$container->set('UserController', function($c) {
    $view = $c->get("view"); // retrieve the 'view' from the container
    $em = $c->get(EntityManager::class);
    return new UserController($view, $em);
});

$container->set('UserSessionController', function($c) {
    $view = $c->get("view"); // retrieve the 'view' from the container
    return new UserSessionController($view);
});

// Add route
$app->get('/', 'SessionController:listAll'); //listar sesiones
$app->get('/sessions/{idSession:[0-9]+}', 'SessionController:list'); //sesion especifica
$app->get('/sessions', 'SessionController:listAll'); //listar sesiones
$app->post('/sessions', 'SessionController:add'); //nueva sesion si vengo a esta direccion por post
$app->get('/sessions/form', 'SessionController:form'); //formulario para nueva sesion
$app->get('/sessions/{idSession}/remove', 'SessionController:delete'); 
$app->post('/sessions/{idSession}/update', 'SessionController:update');

$app->get('/sessions/{idSession}/buyins/', 'BuyinSessionController:listAll');
$app->get('/sessions/{idSession}/buyins/{idbuyin}', 'BuyinSessionController:list');
$app->post('/sessions/{idSession}/buyins', 'BuyinSessionController:add');
$app->get('/{idSession}/buyins/form', 'BuyinSessionController:form'); //formulario para nueva sesion
$app->get('/{idSession}/buyins/{idbuyin}/remove', 'BuyinSessionController:delete'); 
$app->post('/buyins/{idBuyin}/update', 'BuyinSessionController:update');



$app->get('/sessions/{idSession}/comissions/', 'ComissionSessionController:listAll');
$app->get('/sessions/{idSession}/comissions/{idcomission}', 'ComissionSessionController:list'); //{idcomission:[0-9]} no funciona
$app->post('/sessions/{idSession}/comissions', 'ComissionSessionController:add');
$app->get('/{idSession}/comissions/form', 'ComissionSessionController:form'); //formulario para nueva sesion
$app->get('/{idSession}/comissions/{idcomission}/remove', 'ComissionSessionController:delete');
$app->post('/comissions/{idcomission}/update', 'ComissionSessionController:update');

$app->get('/sessions/{idSession}/tips/', 'TipSessionController:listAll');
$app->get('/sessions/{idSession}/tips/dealerTip/{idDealerTip}', 'TipSessionController:list'); //{idcomission:[0-9]} no funciona
$app->get('/sessions/{idSession}/tips/serviceTip/{idServiceTip}', 'TipSessionController:list');
$app->post('/sessions/{idSession}/tips', 'TipSessionController:add');
$app->get('/{idSession}/tips/form', 'TipSessionController:form'); //formulario para nueva sesion
$app->get('/tips/dealertip/{idDealerTip}/remove', 'TipSessionController:delete');
$app->get('/tips/servicetip/{idServiceTip}/remove', 'TipSessionController:delete');
$app->post('/tips/dealertip/{idDealerTip}/update', 'TipSessionController:update');
$app->post('/tips/servicetip/{idServiceTip}/update', 'TipSessionController:update');

$app->get('/sessions/{idSession}/usersSession/', 'UserSessionController:listAll');
$app->get('/sessions/{idSession}/usersSession/{idusersession}', 'UserSessionController:list'); //{idcomission:[0-9]} no funciona
$app->post('/sessions/{idSession}/usersSession', 'UserSessionController:add');
$app->get('/{idSession}/usersSession/form', 'UserSessionController:form'); //formulario para nueva sesion
$app->get('/{idSession}/usersSession/{idusersession}/remove', 'UserSessionController:delete');
$app->post('/usersSession/{idusersession}/update', 'UserSessionController:update');
$app->get('/userssession/{idusersession}/formclose', 'UserSessionController:formClose');
$app->post('/userssession/{idusersession}/close', 'UserSessionController:close');

$app->get('/users/', 'UserController:listAll');
$app->get('/users/{iduser}', 'UserController:list'); //{idcomission:[0-9]} no funciona
$app->post('/users/', 'UserController:add');
$app->get('/form', 'UserController:form'); //formulario para nueva sesion
$app->get('/users/{iduser}/remove', 'UserController:delete');
$app->post('/users/{iduser}/update', 'UserController:update');

$app->run();