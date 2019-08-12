<?php

declare(strict_types=1);

use App\Application\Middleware\SessionMiddleware;
use Slim\App;
use Solcre\lmsuy\Middleware\CheckAuthenticationMiddleware;
use Solcre\lmsuy\Middleware\ViewSelectorMiddleware;
use Solcre\lmsuy\View\TwigWrapperView;
use Slim\Views\Twig;
use Solcre\lmsuy\View\JsonView;
use Slim\Middleware\ErrorMiddleware;
use Middlewares\ContentType;

return function (App $app) {

    $container = $app->getContainer();

    $responseFactory = $app->getResponseFactory();

    $settings = $container->get('settings');
    // $twigView = new \Slim\Views\Twig('templates');
    $twigView =  new  Twig('../templates');
    $environment = $twigView->getEnvironment();
    $extension = $environment->getExtension(\Twig\Extension\CoreExtension::class);
    $extension->setNumberFormat(
        $settings['number_format']['decimals'],
        $settings['number_format']['decimal_separator'],
        $settings['number_format']['thousand_separator']
    );
    
    $twigWrapperView = new TwigWrapperView($twigView);
    //add CheckAuthenticationMiddleware
    // $checkAuthenticationMiddleware = new CheckAuthenticationMiddleware($container);
    // $app->add($checkAuthenticationMiddleware);

    $viewMap = [
        'text/html'        => $twigWrapperView,
        'application/json' => new JsonView()// JsonView
    ];

    //add ViewSelectorMiddleware
    $viewSelectorMiddleware = new ViewSelectorMiddleware($container, $viewMap);
    $app->add($viewSelectorMiddleware);

    // Add content middleware
    $negotiationMiddleware = new ContentType(null, $responseFactory);
    $app->add($negotiationMiddleware);

/*
    // Add error middleware
    $errorMiddleware = new ErrorMiddleware(
        $app->getCallableResolver(),
        $responseFactory,
        getenv('ENVIRONMENT')=='dev',
        true,
        true
    );
    $app->add($errorMiddleware);
*/
};
