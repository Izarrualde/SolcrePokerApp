<?php

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Solcre\Pokerclub\Service\SessionService;
use Solcre\Pokerclub\Entity\SessionEntity;
use Solcre\lmsuy\Controller\SessionController;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;
use Test\AppWrapper;
use Solcre\Pokerclub\Exception\SessionNotFoundException;
use Solcre\Pokerclub\Exception\SessionInvalidException;

class SessionControllerTest extends TestCase 
{
    public function createController($view, $sessionService) 
    {
        $container = AppWrapper::getContainer();

        // Get EntityManager from container
        $entityManager = $container->get(EntityManager::class);

        $viewMock = 
        $controller = new SessionController($view, $entityManager);

        // Inject the mocked sessionService by reflection
        $reflection = new ReflectionProperty($controller, 'sessionService');
        $reflection->setAccessible(true);
        $reflection->setValue($controller, $sessionService);

        return $controller;
    }

    public function getAListOfSessions() 
    {
        return [
            new SessionEntity(
                1,
                date_create('2019-06-26 15:00:00'),
                'test session',
                'test description',
                'test photo',
                9,
                date_create('2019-06-26 19:00:00'),
                date_create('2019-06-26 19:30:00'),
                date_create('2019-06-26 23:30:00')
            ),
            new SessionEntity(
                2,
                date_create('2019-06-27 15:00:00'),
                'another test session',
                'another test description',
                'another test photo',
                10,
                date_create('2019-06-27 19:00:00'),
                date_create('2019-06-27 19:30:00'),
                date_create('2019-06-27 23:30:00')
            ),
            new SessionEntity(
                3,
                date_create('2019-06-28 15:00:00'),
                'one more test session',
                'one more test description',
                'one more test photo',
                6,
                date_create('2019-06-28 19:00:00'),
                date_create('2019-06-28 19:30:00'),
                date_create('2019-06-28 23:30:00')
            )
        ];
    }

    public function listAllSetup($view)
    {
        $sessionService = $this->createMock(SessionService::class);

        $expectedSessions = $this->getAListOfSessions();

        $sessionService->method('fetchAll')->willReturn($expectedSessions);

        $controller = $this->createController($view, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedSessions as $session) {
          $expectedSessionsArray[] = $session->toArray();
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = $expectedSessionsArray;
        }

        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI 
        ];
    }


    public function testListAllWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $request  = $this->createMock(Slim\Psr7\Request::class);
        
        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response->withStatus(200);

        $setup           = $this->listAllSetup($view);
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

    public function listSetup($view)
    {
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

        $sessionService->method('fetch')->willReturn($expectedSession);

        $controller = $this->createController($view, $sessionService);

        $expectedDatosUI = [];

        if ($view instanceof JsonView) {
            $expectedDatosUI = $expectedSession->toArray();
        }

        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI 
        ];
    }


    public function listWithExceptionSetup($view, $exception)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $sessionService = $this->createMock(SessionService::class);

        $sessionService->method('fetch')->will($this->throwException($exception));

        $controller = $this->createController($view, $sessionService);

        $expectedDatosUI = [];

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse 
        ];
    }

    public function testListWithJsonView()
    {
        $view = $this->createMock(JsonView::class);
    
        $request          = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response->withStatus(200);

        $args = [
          'idSession' => 1
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
        )           ;  

        $controller->list($request, $response, $args);
    }

    public function testListWithSessionNotFoundWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 1
        ];

        $exception = new SessionNotFoundException();

        $setup            = $this->listWithExceptionSetup($view, $exception);
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
            $this->equalTo($expectedDatosUI),
        );

        $controller->list($request, $response, $args);
    }

    public function testListWithExceptionWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 1
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404);

        $setup            = $this->listWithExceptionSetup($view, $exception);
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
            $this->equalTo($expectedDatosUI),
        );

        $controller->list($request, $response, $args);
    }

    public function addSetup($view)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getParsedBody')->willReturn(
          [
             'idSession'     => 3,
             'date'          => '2019-06-26T19:05',
             'title'         => 'test session',
             'description'   => 'test description',
             'photo'         => 'test photo',
             'seats'         => 9,
             'startTime'     => '2019-06-26 15:00:00',
             'startTimeReal' => '2019-06-26 15:00:00',
             'endTime'       => '2019-06-26 23:00:00'
          ]
        );

        $response         = new Slim\Psr7\Response();

        $sessionService = $this->createMock(SessionService::class);

        $post = $request->getParsedBody();

        $expectedSessionAdded = new SessionEntity(
          $post['idSession'],
          date_create($post['date']),
          $post['title'],
          $post['description'],
          $post['photo'],
          $post['seats'],
          date_create($post['startTime']),
          date_create($post['startTimeReal']),
          date_create($post['endTime'])
        );

        $expectedSessions = [
            new SessionEntity(
              1,
              date_create('2019-06-26T19:05'),
              'title1',
              'description1',
              'photo1',
              'seats1',
              date_create('2019-06-26T20:05'),
              date_create('2019-06-26T20:05'),
              date_create('2019-06-26T23:05')
            ),
            $expectedSessionAdded
        ];

        $sessionService->method('fetchAll')->willReturn($expectedSessions);
        $sessionService->method('add')->willReturn($expectedSessionAdded);

        $controller = $this->createController($view, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedSessions as $session) {
          $expectedSessionsArray[] = $session->toArray();
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = $expectedSessionAdded->toArray();
             $expectedResponse = $response->withStatus(201);
        }

        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function addAndUpdateWithExceptionSetup($view, $exception)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getParsedBody')->willReturn(
          [
             'idSession'     => 3,
             'date'          => '2019-06-26T19:05',
             'title'         => 'test session',
             'description'   => 'test description',
             'photo'         => 'test photo',
             'seats'         => 9,
             'startTime'     => '2019-06-26 15:00:00',
             'startTimeReal' => '2019-06-26 15:00:00',
             'endTime'       => '2019-06-26 23:00:00'
          ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $sessionService = $this->createMock(SessionService::class);

        $expectedSessions = $this->getAListOfSessions();

        $sessionService->method('fetchAll')->willReturn($expectedSessions);
        $sessionService->method('add')->will($this->throwException($exception));
        $sessionService->method('update')->will($this->throwException($exception));

        $controller = $this->createController($view, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedSessions as $session) {
          $expectedSessionsArray[] = $session->toArray();
        }

        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function testAddWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [];

        // metthod with data post use 2 parameters in addSetup
        $setup            = $this->addSetup($view);
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

        $controller->add($request, $response, $args);
    }

    public function testAddWithInvalidExceptionWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [];

        $exception = new SessionInvalidException();

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
        $view = $this->createMock(JsonView::class);

        $args = [];

        $exception = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404); 

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
             'idSession'     => 3,
             'date'          => '2019-06-26T19:05',
             'title'         => 'test session',
             'description'   => 'test description',
             'photo'         => 'test photo',
             'seats'         => 9,
             'startTime'     => '2019-06-26 15:00:00',
             'startTimeReal' => '2019-06-26 15:00:00',
             'endTime'       => '2019-06-26 23:00:00'
          ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $post = $request->getParsedBody();

        $sessionService = $this->createMock(SessionService::class);

        $expectedSessionUpdated = new SessionEntity(
          $post['idSession'],
          date_create($post['date']),
          $post['title'],
          $post['description'],
          $post['photo'],
          $post['seats'],
          date_create($post['startTime']),
          date_create($post['startTimeReal']),
          date_create($post['endTime'])
        );

        $expectedSessions = [
            new SessionEntity(
              1,
              date_create('2019-06-26T19:05'),
              'title1',
              'description1',
              'photo1',
              'seats1',
              date_create('2019-06-26T20:05'),
              date_create('2019-06-26T20:05'),
              date_create('2019-06-26T23:05')
            ),
            $expectedSessionUpdated
        ];

        $sessionService->method('fetchAll')->willReturn($expectedSessions);
        $sessionService->method('update')->willReturn($expectedSessionUpdated);

        $controller = $this->createController($view, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedSessions as $session) {
          $expectedSessionsArray[] = $session->toArray();
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI  = $expectedSessionUpdated->toArray();
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

        $args = [];

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

    public function testUpdateWithInvalidSessionWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [];

        $exception = new SessionInvalidException();

        $setup            = $this->addAndupdateWithExceptionSetup($view, $exception);
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


    public function testUpdateWithSessionNotFoundWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [];

        $exception = new SessionNotFoundException();

        $setup            = $this->addAndupdateWithExceptionSetup($view, $exception);
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
            $this->equalTo($expectedDatosUI),
        );

        $controller->update($request, $response, $args);
    }

    public function testUpdateWithExceptionJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [];

        $exception = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404); 

        $setup            = $this->addAndupdateWithExceptionSetup($view, $exception);
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

        $sessionService = $this->createMock(SessionService::class);

        $expectedSessions = $this->getAListOfSessions();

        $sessionService->method('fetchAll')->willReturn($expectedSessions);
        $sessionService->method('delete')->willReturn(true);

        $controller = $this->createController($view, $sessionService);

        $expectedDatosUI = null;

        foreach ($expectedSessions as $session) {
          $sessions[] = $session->toArray();
        }

        if ($view instanceof JsonView) {
            $expectedResponse = $expectedResponse->withStatus(204);
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }


    public function deleteWithExceptionSetup($view, $exception)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $sessionService = $this->createMock(SessionService::class);

        $expectedSessions = $this->getAListOfSessions();

        $sessionService->method('fetchAll')->willReturn($expectedSessions);
        $sessionService->method('delete')->will($this->throwException($exception));

        $controller = $this->createController($view, $sessionService);

        $expectedDatosUI = null;

        foreach ($expectedSessions as $session) {
          $sessions[] = $session->toArray();
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function testDeleteWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 1
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

    public function testDeleteSessionNotFoundWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 1
        ];

        $exception = new SessionNotFoundException();

        $setup            = $this->deleteWithExceptionSetup($view, $exception);
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
            $this->equalTo($expectedDatosUI),
        );

        $controller->delete($request, $response, $args);
    }
   
    public function testDeleteWithExceptionWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 1
        ];

        $exception = new \Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404); 

        $setup            = $this->deleteWithExceptionSetup($view, $exception);
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

        $controller->delete($request, $response, $args);
    }

    public function calculatePointsSetup($view)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $sessionService = $this->createMock(SessionService::class);

        $expectedSessions = $this->getAListOfSessions();

        $sessionService->method('fetchAll')->willReturn($expectedSessions);
        $sessionService->method('calculateRakeback')->willReturn(true);

        $controller = $this->createController($view, $sessionService);

        $expectedDatosUI = null;

        foreach ($expectedSessions as $session) {
          $sessions[] = $session->toArray();
        }

        if ($view instanceof JsonView) {
            $expectedResponse = $response->withStatus(200);
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function calculatePointsWithExceptionSetup($view, $exception)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $sessionService = $this->createMock(SessionService::class);

        $expectedSessions = $this->getAListOfSessions();

        $sessionService->method('fetchAll')->willReturn($expectedSessions);
        $sessionService->method('calculateRakeback')->will($this->throwException($exception));

        $controller = $this->createController($view, $sessionService);

        $expectedDatosUI = null;

        foreach ($expectedSessions as $session) {
          $sessions[] = $session->toArray();
        }

        if ($view instanceof JsonView) {
            $expectedResponse = $response->withStatus(200);
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function testCalcuatePointsWithJsonView()
    {
        // en principio testeo que devuelva el correcto datosUI y por consiguiente el msj correcto, y para json que de el correcto statusCode y datosUI null

        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 1
        ];

        $setup            = $this->calculatePointsSetup($view);
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

        $controller->calculatePoints($request, $response, $args);
    }

    public function testCalcuatePointsWithSessionNotFoundWithJsonView()
    {
        // en principio testeo que devuelva el correcto datosUI y por consiguiente el msj correcto, y para json que de el correcto statusCode y datosUI null

        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 1
        ];

        $exception = new SessionNotFoundException();

        $setup            = $this->calculatePointsWithExceptionSetup($view, $exception);
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
            $this->equalTo($expectedDatosUI),
        );

        $controller->calculatePoints($request, $response, $args);
    }

    public function testCalcuatePointsWithExceptionWithJsonView()
    {
        // en principio testeo que devuelva el correcto datosUI y por consiguiente el msj correcto, y para json que de el correcto statusCode y datosUI null. y que haya llamado a calcuateRakeback
        // puedo asumir que calculate rakeback trabaja bien. tambien podria chequearlo.

        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 1
        ];

        $exception = new \Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404);

        $setup            = $this->calculatePointsWithExceptionSetup($view, $exception);
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

        $controller->calculatePoints($request, $response, $args);
    }
}
