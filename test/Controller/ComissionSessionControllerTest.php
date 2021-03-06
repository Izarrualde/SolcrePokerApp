<?php

use PHPUnit\Framework\TestCase;
// use ReflectionMethod;
use \Solcre\Pokerclub\Service\ComissionSessionService;
use \Solcre\Pokerclub\Service\SessionService;
use \Solcre\Pokerclub\Entity\ComissionSessionEntity;
use \Solcre\Pokerclub\Entity\SessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;
use Solcre\Pokerclub\Exception\ComissionInvalidException;
use Solcre\Pokerclub\Exception\ComissionNotFoundException;
use Test\AppWrapper;
use Solcre\lmsuy\Controller\ComissionSessionController;
use Doctrine\Common\Collections\ArrayCollection;

class ComissionSessionControllerTest extends TestCase
{

    public function createController($view, $comissionService, $sessionService)
    {
        $container = AppWrapper::getContainer();

        // Get EntityManager from container
        $entityManager = $container->get(EntityManager::class);

        $controller = new ComissionSessionController($view, $entityManager);

        // Inject the mocked comissionService by reflection
        $reflection = new ReflectionProperty($controller, 'comissionService');
        $reflection->setAccessible(true);
        $reflection->setValue($controller, $comissionService);

        // Inject the mocked sessionService by reflection
        $reflection = new ReflectionProperty($controller, 'sessionService');
        $reflection->setAccessible(true);
        $reflection->setValue($controller, $sessionService);

        return $controller;
    }

    public function getAListOfComissions($session)
    {
        return [ 
            new ComissionSessionEntity(
              1,
              date_create('2019-06-26 19:05:00'),
              50,
              $session
            ),
            new ComissionSessionEntity(
              2,
              date_create('2019-06-26 19:10:00'),
              60,
              $session
            ),
          ];
    }

    public function listAllSetup($view)
    {
          $comissionService = $this->createMock(ComissionSessionService::class);
          $sessionService = $this->createMock(SessionService::class);
         
          $expectedSession = new SessionEntity(
            2,
            date_create('2019-06-27 15:00:00'),
            'another test session',
            'another test description',
            'another test photo',
            10,
            date_create('2019-06-27 19:00:00'),
            date_create('2019-06-27 19:30:00'),
            date_create('2019-06-27 23:30:00')
          );

          $expectedComissions = $this->getAListOfComissions($expectedSession);

          $sessionService->method('fetch')->willReturn($expectedSession);
          $comissionService->method('fetchAll')->willReturn($expectedComissions);

          $controller = $this->createController($view, $comissionService, $sessionService); 
          $expectedDatosUI = [];

          foreach ($expectedComissions as $comission) {
            $expectedComissionsArray[] = $comission->toArray();
          }

          if ($view instanceof JsonView) {
              $expectedDatosUI = $expectedComissionsArray;
          }

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

        $args = [
            'idSession' => 2
        ];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->listAll($request, $response, $args);
    }

    public function listAllWithExceptionSetup($view, $exception)
    {
          $comissionService = $this->createMock(ComissionSessionService::class);
          $sessionService = $this->createMock(SessionService::class);


          $sessionService->method('fetch')->will($this->throwException($exception));

          $comissionService->method('fetchAll')->willReturn(null);

          $controller = $this->createController($view, $comissionService, $sessionService); 
          
          $expectedDatosUI = [];

          return [ 
              'controller'      => $controller, 
              'expectedDatosUI' => $expectedDatosUI 
          ];
      }

    public function testListAllWithExceptionWithJsonView()
    {
        $view     = $this->createMock(JsonView::class);
        $request  = $this->createMock(Slim\Psr7\Request::class);
        
        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response->withStatus(500);

        $exception = new \Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404);

        $setup           = $this->listAllWithExceptionSetup($view, $exception);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];

        $args = [
            'idSession' => 2
        ];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->listAll($request, $response, $args);
    } 

    public function listSetup($view)
    {
        $comissionService = $this->createMock(ComissionSessionService::class);
        $sessionService   = $this->createMock(SessionService::class);

        $expectedSession = new SessionEntity(
            2,
            date_create('2019-06-27 15:00:00'),
            'another test session',
            'another test description',
            'another test photo',
            10,
            date_create('2019-06-27 19:00:00'),
            date_create('2019-06-27 19:30:00'),
            date_create('2019-06-27 23:30:00')
        );

        $expectedComission = new ComissionSessionEntity(
            1,
            date_create('2019-06-26 19:05:00'),
            50,
            $expectedSession
        );

        $sessionService->method('fetch')->willReturn($expectedSession);
        $comissionService->method('fetch')->willReturn($expectedComission);

        $controller = $this->createController($view, $comissionService, $sessionService);

        $expectedDatosUI = [];

        if ($view instanceof JsonView) {
            $expectedDatosUI = $expectedComission->toArray();
        }

        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI 
        ];
    }

    public function listWithExceptionSetup($view, $exception)
    {
        $comissionService = $this->createMock(ComissionSessionService::class);
        $sessionService   = $this->createMock(SessionService::class);

        $sessionService->method('fetch')->willReturn(null);
        $comissionService->method('fetch')->will($this->throwException($exception));

        $controller = $this->createController($view, $comissionService, $sessionService);

        $expectedDatosUI = [];

        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI 
        ];
    }

    public function testListWithJsonView()
    {
        // check that invoke controller listAll with parameters $request, $response and $datosUI
        // response contains the right statusCode 

        $view    = $this->createMock(JsonView::class);
        $request = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response->withStatus(200);

        $args = [
          'idSession'   => 2,
          'idcomission' => 1
        ];

        $setup           = $this->listSetup($view);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];


        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->list($request, $response, $args);
    }

    public function testListWithComissionNotFoundExceptionWithJsonView()
    {
        $view    = $this->createMock(JsonView::class);
        $request = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response->withStatus(404);

        $args = [
          'idSession'   => 2,
          'idcomission' => 1
        ];

        $exception = New ComissionNotFoundException();

        $setup           = $this->listWithExceptionSetup($view, $exception);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->list($request, $response, $args);
    }

    public function testListWithExceptionWithJsonView()
    {
        $view    = $this->createMock(JsonView::class);
        $request = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response->withStatus(500);

        $args = [
          'idSession'   => 2,
          'idcomission' => 1
        ];

        $exception = new Exception();

        $setup           = $this->listWithExceptionSetup($view, $exception);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
            //$this->contains([$exception->getMessage()]),
        );

        $controller->list($request, $response, $args);
    }

    public function addSetup($view)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);

        $request->method('getParsedBody')->willReturn(
            [
              'comission' => 30,
              'idSession' => 2,
              'hour'      => '2019-06-26 19:05:00'
            ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $comissionService = $this->createMock(ComissionSessionService::class);
        $sessionService   = $this->createMock(SessionService::class);

        $post = $request->getParsedBody();

        $expectedSession = new SessionEntity(
            $post['idSession'],
            date_create('2019-06-27 15:00:00'),
            'another test session',
            'another test description',
            'another test photo',
            10,
            date_create('2019-06-27 19:00:00'),
            date_create('2019-06-27 19:30:00'),
            date_create('2019-06-27 23:30:00')
        );

        $expectedComissionAdded = new ComissionSessionEntity(
            2,
            $post['comission'],
            date_create($post['hour']),
            $expectedSession,
        );

        $expectedComissions = [
            new ComissionSessionEntity(
                1,
                date_create('2019-06-26 19:00:00'),
                50,
                $expectedSession
            ),
            $expectedComissionAdded
        ];

        $sessionService->method('fetch')->willReturn($expectedSession);
        $comissionService->method('fetchAll')->willReturn($expectedComissions);
        $comissionService->method('add')->willReturn($expectedComissionAdded);

        $controller = $this->createController($view, $comissionService, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedComissions as $comission) {
            $expectedComissionsArray[] = $comission->toArray();
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = $expectedComissionAdded->toArray();
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function testAddWithJsonView()
    {
        $view    = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        // metthod with data post use 2 parameters in addSetup
        $setup            = $this->addSetup($view);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $response->withStatus(201);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->add($request, $response, $args);
    }

    public function addAndUpdateWithExceptionSetup($view, $exception)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);

        $request->method('getParsedBody')->willReturn(
            [
              'comission' => 'a non numeric value',
              'idSession' => 2,
              'hour'      => '2019-06-26 19:05:00'
            ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;
        $comissionService = $this->createMock(ComissionSessionService::class);
        $sessionService   = $this->createMock(SessionService::class);

        $expectedSession = new SessionEntity(1);

        $expectedComissions = $this->getAListOfComissions($expectedSession);

        $sessionService->method('fetch')->willReturn($expectedSession);
        $comissionService->method('fetchAll')->willReturn($expectedComissions);
        
        $comissionService->method('add')->will($this->throwException($exception));
        $comissionService->method('update')->will($this->throwException($exception));

        $controller = $this->createController($view, $comissionService, $sessionService);
        $expectedDatosUI = [];

        if (is_array($expectedComissions)) {
             foreach ($expectedComissions as $comission) {
                $expectedComissionsArray[] = $comission->toArray();
            }           
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function testAddWithInvalidComissionWithJsonView() 
    {
        $view    = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new ComissionInvalidException();
        
        $setup            = $this->addAndUpdateWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $response->withStatus(400);


        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

      $controller->add($request, $response, $args);
    }

    public function testAddWithExceptionWithJsonView() 
    {
        $view    = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\ComissionSessionEntity' . " Entity not found", 404); 

        $setup            = $this->addAndUpdateWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $response->withStatus(500);


        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

      $controller->add($request, $response, $args);
    }

    public function updateSetup($view)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);

        $request->method('getParsedBody')->willReturn(
            [
              'comission' => 30,
              'idSession' => 2,
              'hour'      => '2019-06-26 19:05:00'
            ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $post = $request->getParsedBody();

        $comissionService = $this->createMock(ComissionSessionService::class);
        $sessionService   = $this->createMock(SessionService::class);

        $expectedSession = new SessionEntity(
            $post['idSession'],
            date_create('2019-06-27 15:00:00'),
            'another test session',
            'another test description',
            'another test photo',
            10,
            date_create('2019-06-27 19:00:00'),
            date_create('2019-06-27 19:30:00'),
            date_create('2019-06-27 23:30:00')
        );

        $expectedComissionUpdated = new ComissionSessionEntity(
            2,
            $post['comission'],
            date_create($post['hour']),
            $expectedSession,
        );

        $expectedComissions = [
            new ComissionSessionEntity(
                1,
                date_create('2019-06-26 19:00:00'),
                50,
                $expectedSession
            ),
            $expectedComissionUpdated
        ];

        $sessionService->method('fetch')->willReturn($expectedSession);
        $comissionService->method('update')->willReturn($expectedComissionUpdated);
        $comissionService->method('fetchAll')->willReturn($expectedComissions);

        $controller = $this->createController($view, $comissionService, $sessionService);
        
        $expectedDatosUI = [];

        foreach ($expectedComissions as $comission) {
            $expectedComissionsArray[] = $comission->toArray();
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI  = $expectedComissionUpdated->toArray();
            $expectedResponse = $expectedResponse->withStatus(200);
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];

    }

    public function testUpdateWithJsonView() 
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession'   => 2,
          'idcomission' => 1
        ];

        $setup            = $this->updateSetup($view);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $setup['expectedResponse'];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->update($request, $response, $args);
    }

    public function testUpdateWithInvalidComissionWithJsonView() 
    {
        $view    = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new ComissionInvalidException();

        $setup            = $this->addAndUpdateWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $response->withStatus(400);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

      $controller->update($request, $response, $args);
    }

    public function testUpdateWithComissionNotFoundWithJsonView() 
    {
        $view    = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new ComissionNotFoundException();

        $setup            = $this->addAndUpdateWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $response->withStatus(404);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            //$this->equalTo($expectedDatosUI),
        );

      $controller->update($request, $response, $args);
    }

    public function testUpdateWithExceptionWithJsonView() 
    {
        $view    = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\ComissionSessionEntity' . " Entity not found", 404); 

        $setup            = $this->addAndUpdateWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $response->withStatus(500);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

      $controller->update($request, $response, $args);
    }

    public function deleteSetup($view)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $comissionService = $this->createMock(ComissionSessionService::class);
        $sessionService   = $this->createMock(SessionService::class);

        $expectedSession = new SessionEntity(
            2,
            date_create('2019-06-27 15:00:00'),
            'another test session',
            'another test description',
            'another test photo',
            10,
            date_create('2019-06-27 19:00:00'),
            date_create('2019-06-27 19:30:00'),
            date_create('2019-06-27 23:30:00')
        );

        $expectedComissions = $this->getAListOfComissions($expectedSession);

        $sessionService->method('fetch')->willReturn($expectedSession);
        $comissionService->method('fetchAll')->willReturn($expectedComissions);
        $comissionService->method('delete')->willReturn(true);

        $controller = $this->createController($view, $comissionService, $sessionService);

        $expectedDatosUI = null;

        foreach ($expectedComissions as $comission) {
            $comissions[] = $comission->toArray();
        }

        if ($view instanceof JsonView) {
            $expectedResponse = $expectedResponse->withStatus(204);
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' =>  $expectedResponse
        ];
    }

    public function deleteWithExceptionSetup($view, $exception)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $comissionService = $this->createMock(ComissionSessionService::class);
        $sessionService   = $this->createMock(SessionService::class);

        $expectedSession = new SessionEntity(
            2,
            date_create('2019-06-27 15:00:00'),
            'another test session',
            'another test description',
            'another test photo',
            10,
            date_create('2019-06-27 19:00:00'),
            date_create('2019-06-27 19:30:00'),
            date_create('2019-06-27 23:30:00')
        );

        $expectedComissions = $this->getAListOfComissions($expectedSession);

        $sessionService->method('fetch')->willReturn($expectedSession);
        $comissionService->method('fetchAll')->willReturn($expectedComissions);
        $comissionService->method('delete')->will($this->throwException($exception));

        $controller = $this->createController($view, $comissionService, $sessionService);

        $expectedDatosUI = null;

        foreach ($expectedComissions as $comission) {
            $comissions[] = $comission->toArray();
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' =>  $expectedResponse
        ];
    }

    public function testDeleteWithJsonView() 
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession'   => 2,
          'idcomission' => 1
        ];

        $setup            = $this->deleteSetup($view);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $setup['expectedResponse'];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->delete($request, $response, $args);
    }


    public function testDeleteWithComissionNotFoundWithJsonView() 
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession'   => 2,
          'idcomission' => 1
        ];

        $exception = new ComissionNotFoundException();

        $setup            = $this->deleteWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $setup['expectedResponse']->withStatus(404);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->delete($request, $response, $args);
    }

    public function testDeleteWithExceptionWithJsonView() 
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession'   => 2,
          'idcomission' => 1
        ];

        $exception = new \Exception('Solcre\Pokerclub\Entity\ComissionSessionEntity' . " Entity not found", 404); 

        $setup            = $this->deleteWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $setup['expectedResponse']->withStatus(500);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->delete($request, $response, $args);
    }
}
