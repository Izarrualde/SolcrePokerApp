<?php

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Solcre\Pokerclub\Service\SessionService;
use Solcre\Pokerclub\Entity\SessionEntity;
use Solcre\lmsuy\Controller\SessionController;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;
use Test\AppWrapper;

class SessionControllerTest extends TestCase 
{
/*
  public function testCreateWithParams()
  {

  }
*/
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
  public function testListAll()
  {
    $view = $this->createMock(Slim\Views\Twig::class);
    $sessionService = $this->createMock(SessionService::class);

    $expectedSessions = $this->getAListOfSessions();
    $sessionService->method('fetchAll')->willReturn($expectedSessions);

    $controller = $this->createController($view, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [];

    $expectedDatosUI = [];
    foreach ($expectedSessions as $session) {
      $expectedDatosUI['sessions'][] = $session->toArray();
    }

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo('index.html.twig'),
        $this->equalTo($expectedDatosUI),
    );

    $controller->listAll($request, $response, $args);
  }

  public function testList()
  {
    $view = $this->createMock(Slim\Views\Twig::class);
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

    $sessionService->method('fetchOne')->willReturn($expectedSession);

    $controller = $this->createController($view, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession' => 2
    ];


    $expectedDatosUI = [];
    $expectedDatosUI['session'] = $expectedSession->toArray();
    $expectedDatosUI['breadcrumb'] = 'Editar Sesión';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo('editSession.html.twig'),
        $this->equalTo($expectedDatosUI),
    );

    $controller->list($request, $response, $args);
  }



  public function testAdd() // WhenIsAdded
  {
    $view = $this->createMock(Slim\Views\Twig::class);
    $sessionService = $this->createMock(SessionService::class);

    $expectedSessions = $this->getAListOfSessions();

    $sessionService->method('fetchAll')->willReturn($expectedSessions);
    $sessionService->method('update')->willReturn(true);

    $controller = $this->createController($view, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      [
         'idSession' => 3,
         'date' => '2019-06-26T19:05',
         'title' => 'test session',
         'description' =>    'test description',
         'photo' =>   'test photo',
         'seats' =>  9,
         'startTime'  =>     '2019-06-26 15:00:00',
         'startTimeReal'  =>       '2019-06-26 15:00:00',
         'endTime'  =>     '2019-06-26 23:00:00'
      ]
    );

    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [];

    $expectedDatosUI = [];

    foreach ($expectedSessions as $session) {
      $sessions[] = $session->toArray();
    }

    $expectedDatosUI['sessions'] = $sessions;
    $expectedDatosUI['message'] = ['La sesión se agregó exitosamente'];

    $template = 'index.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->add($request, $response, $args);
  }

  public function testForm()
  {
    $view = $this->createMock(Slim\Views\Twig::class);
    $sessionService = $this->createMock(SessionService::class);


    $controller = $this->createController($view, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [];

    $expectedDatosUI = [];

    $template    = 'newsession.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->form($request, $response, $args);

  }

  public function testUpdate() // WhenIsAdded
  {
    $view = $this->createMock(Slim\Views\Twig::class);
    $sessionService = $this->createMock(SessionService::class);

    $expectedSessions = $this->getAListOfSessions();

    $sessionService->method('fetchAll')->willReturn($expectedSessions);
    $sessionService->method('update')->willReturn(true);

    $controller = $this->createController($view, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      [
         'idSession' => 3,
         'date' => '2019-06-26T19:05',
         'title' => 'test session',
         'description' =>    'test description',
         'photo' =>   'test photo',
         'seats' =>  9,
         'startTime'  =>     '2019-06-26 15:00:00',
         'startTimeReal'  =>       '2019-06-26 15:00:00',
         'endTime'  =>     '2019-06-26 23:00:00'
      ]
    );

    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [];

    $expectedDatosUI = [];

    foreach ($expectedSessions as $session) {
      $sessions[] = $session->toArray();
    }

    $expectedDatosUI['sessions'] = $sessions;
    $expectedDatosUI['message'] = ['La Sesión se actualizó exitosamente'];

    $template = 'index.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->update($request, $response, $args);
  }

  public function testDelete() // WhenIsAdded
  {
    $view = $this->createMock(Slim\Views\Twig::class);
    $sessionService = $this->createMock(SessionService::class);

    $expectedSessions = $this->getAListOfSessions();

    $sessionService->method('fetchAll')->willReturn($expectedSessions);
    $sessionService->method('delete')->willReturn(true);

    $controller = $this->createController($view, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      [
         'idSession' => 3,
         'date' => '2019-06-26T19:05',
         'title' => 'test session',
         'description' =>    'test description',
         'photo' =>   'test photo',
         'seats' =>  9,
         'startTime'  =>     '2019-06-26 15:00:00',
         'startTimeReal'  =>       '2019-06-26 15:00:00',
         'endTime'  =>     '2019-06-26 23:00:00'
      ]
    );

    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession' => 1
    ];

    $expectedDatosUI = [];

    foreach ($expectedSessions as $session) {
      $sessions[] = $session->toArray();
    }

    $expectedDatosUI['sessions'] = $sessions;
    $expectedDatosUI['message'] = ['La Sesión se eliminó exitosamente'];

    $template = 'index.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->delete($request, $response, $args);
  }

}