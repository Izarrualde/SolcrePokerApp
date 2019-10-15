<?php

use PHPUnit\Framework\TestCase;
// use ReflectionMethod;
use Solcre\lmsuy\Service\RakebackService;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;
use Solcre\lmsuy\Exception\PathIsNotDirException;
use Test\AppWrapper;
use Solcre\lmsuy\Controller\RakebackController;
use Doctrine\Common\Collections\ArrayCollection;


class RakebackControllerTest extends TestCase
{

    public function createController($view, $rakebackService)
    {
        $container = AppWrapper::getContainer();

        // Get EntityManager from container

        $controller = new RakebackController($view, $rakebackService);

        // Inject the mocked comissionService by reflection
        $reflection = new ReflectionProperty($controller, 'rakebackService');
        $reflection->setAccessible(true);
        $reflection->setValue($controller, $rakebackService);

        return $controller;
    }


    public function listAllSetup($view)
    {
          $rakebackService = $this->createMock(RakebackService::class);
         
          $expectedAlgorithms = ['algorithm1', 'algorithm2', 'algorithm3'];

          $rakebackService->method('fetchAll')->willReturn($expectedAlgorithms);

          $controller = $this->createController($view, $rakebackService); 

          $expectedDatosUI = $expectedAlgorithms;

          return [ 
              'controller'      => $controller, 
              'expectedDatosUI' => $expectedDatosUI 
          ];
      }

    public function listAllWithExceptionSetup($view, $exception)
    {
          $rakebackService = $this->createMock(RakebackService::class);

          $rakebackService->method('fetchAll')->will($this->throwException($exception));

          $controller = $this->createController($view, $rakebackService); 

          $expectedDatosUI = [];

          return [ 
              'controller'      => $controller, 
              'expectedDatosUI' => $expectedDatosUI 
          ];
      }

    public function testListAllWithJsonView()
    {
        // check that invoke controller listAll with parameters $request, $response and $datosUI
        // response contains the right statusCode 

        $request  = $this->createMock(Slim\Psr7\Request::class);
        $view     = $this->createMock(Solcre\lmsuy\View\JsonView::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response->withStatus(200);

        $setup = $this->listAllSetup($view);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];

        $args = [];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->listAll($request, $response, $args);
    }

    public function testListAllWithJsonViewWithPathIsNotDirException()
    {
        // check that invoke controller listAll with parameters $request, $response and $datosUI
        // response contains the right statusCode 

        $request  = $this->createMock(Slim\Psr7\Request::class);
        $view     = $this->createMock(Solcre\lmsuy\View\JsonView::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response->withStatus(404);

        $exception = new PathIsNotDirException();

        $setup = $this->listAllWithExceptionSetup($view, $exception);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];

        $args = [];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->listAll($request, $response, $args);
    }
}
