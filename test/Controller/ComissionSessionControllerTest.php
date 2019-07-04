<?php

use PHPUnit\Framework\TestCase;
// use ReflectionMethod;
use \Solcre\lmsuy\Service\ComissionSessionService;
use \Solcre\lmsuy\Service\SessionService;
use \Solcre\lmsuy\Entity\ComissionSessionEntity;
use \Solcre\lmsuy\Entity\SessionEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;
use Solcre\lmsuy\Exception\ComissionInvalidException;
use Test\AppWrapper;
use Solcre\lmsuy\Controller\ComissionSessionController;


class ComissionSessionControllerTest extends TestCase
{

  public function createController($view, $comissionService, $sessionService) {
      $container = AppWrapper::getContainer();

      // Get EntityManager from container
      $entityManager = $container->get(EntityManager::class);

      $viewMock = 
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

  public function testListAll()
  {
    $view = $this->createMock(Slim\Views\Twig::class);
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

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $comissionService->method('fetchAll')->willReturn($expectedComissions);

    $controller = $this->createController($view, $comissionService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession' => 2
    ];

    $expectedDatosUI = [];

    foreach ($expectedComissions as $comission) {
      $comissions[] = $comission->toArray();
    }

    $expectedDatosUI['session']           = $expectedSession->toArray();
    $expectedDatosUI['session']['comissions'] = $comissions;
    $expectedDatosUI['breadcrumb']                 = 'Comisiones';

    $template    = 'comissions.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->listAll($request, $response, $args);

  }

  public function testList()
  {
    $view = $this->createMock(Slim\Views\Twig::class);
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

    $expectedComission = new ComissionSessionEntity(
      1,
      date_create('2019-06-26 19:05:00'),
      50,
      $expectedSession
    );

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $comissionService->method('fetchOne')->willReturn($expectedComission);

    $controller = $this->createController($view, $comissionService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession'  => 2,
      'idcomission' => 1
    ];

    $expectedDatosUI = [];

    $expectedDatosUI['session']               = $expectedSession->toArray();
    $expectedDatosUI['session']['comission'] = $expectedComission->toArray();
    $expectedDatosUI['breadcrumb']            = 'Editar Comision';

    $template    = 'editComission.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->list($request, $response, $args);

  }


  public function testAdd() // WhenIsAdded
  {


    $view = $this->createMock(Slim\Views\Twig::class);
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

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $comissionService->method('fetchAll')->willReturn($expectedComissions);
    $comissionService->method('add')->willReturn(true);

    $controller = $this->createController($view, $comissionService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      [
        'idSession' => 2,
        'comission' => 50,
        'hour' => '2019-06-26T19:05'
      ]
    );

    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession' => 2
    ];

    $expectedDatosUI = [];

    foreach ($expectedComissions as $comission) {
      $comissions[] = $comission->toArray();
    }


    $expectedDatosUI['session']               = $expectedSession->toArray();
    $expectedDatosUI['session']['comissions'] = $comissions;
    $expectedDatosUI['breadcrumb']            = 'Comisiones';
    $expectedDatosUI['message']               = ['la comission se ingreso exitosamente.'];

    $template = 'comissions.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->add($request, $response, $args);
  }

  public function testAddWithInvalidComission() 
  {
    $view = $this->createMock(Slim\Views\Twig::class);
    $comissionService = $this->createMock(ComissionSessionService::class);
    $sessionService = $this->createMock(SessionService::class);

    $sessionService->method('fetchOne')->willReturn(null);
    $comissionService->method('fetchAll')->willReturn(null);
    $exception = new ComissionInvalidException();
    $comissionService->method('add')->will($this->throwException($exception));

    $controller = $this->createController($view, $comissionService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      [
        'idSession' => 2,
        'comission' => 'a not numeric value',
        'hour' => '2019-06-26T19:05'
      ]
    );

    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession' => 2
    ];

    $template = 'comissions.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->contains([$exception->getMessage()]),
    );

    $controller->add($request, $response, $args);
  }
  public function testAddWithoutPostData() 
  {
    $view = $this->createMock(Slim\Views\Twig::class);
    $comissionService = $this->createMock(ComissionSessionService::class);
    $sessionService = $this->createMock(SessionService::class);

    $controller = $this->createController($view, $comissionService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(null);

    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession' => 2
    ];

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->anything(), // For example use of a parameter we want to skip assertion
        $this->equalTo([]),
    );

    $controller->add($request, $response, $args);
  }

  public function testForm()
  {
    $view = $this->createMock(Slim\Views\Twig::class);
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

    $sessionService->method('fetchOne')->willReturn($expectedSession);

    $controller = $this->createController($view, $comissionService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession'  => 2
    ];

    $expectedDatosUI = [];

    $expectedDatosUI['session']               = $expectedSession->toArray();
    $expectedDatosUI['breadcrumb']            = 'Nueva Comision';

    $template    = 'newcomissions.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->form($request, $response, $args);

  }

  public function testUpdate() 
  {
    $view = $this->createMock(Slim\Views\Twig::class);
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

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $comissionService->method('fetchAll')->willReturn($expectedComissions);
    $comissionService->method('update')->willReturn(true);

    $controller = $this->createController($view, $comissionService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      [
        'idSession' => 2,
        'comission' => 50,
        'hour' => '2019-06-26T19:05'
      ]
    );

    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession'   => 2,
      'idcomission' => 1
    ];

    $expectedDatosUI = [];

    foreach ($expectedComissions as $comission) {
      $comissions[] = $comission->toArray();
    }


    $expectedDatosUI['session']               = $expectedSession->toArray();
    $expectedDatosUI['session']['comissions'] = $comissions;
    $expectedDatosUI['breadcrumb']            = 'Comisiones';
    $expectedDatosUI['message']               = ['la comission se actualizó exitosamente.'];

    $template = 'comissions.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->update($request, $response, $args);
  }

public function testDelete() 
  {
    $view = $this->createMock(Slim\Views\Twig::class);
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

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $comissionService->method('fetchAll')->willReturn($expectedComissions);
    $comissionService->method('delete')->willReturn(true);

    $controller = $this->createController($view, $comissionService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      [
        'idSession' => 2,
        'comission' => 50,
        'hour' => '2019-06-26T19:05'
      ]
    );

    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession'   => 2,
      'idcomission' => 1
    ];

    $expectedDatosUI = [];

    foreach ($expectedComissions as $comission) {
      $comissions[] = $comission->toArray();
    }


    $expectedDatosUI['session']               = $expectedSession->toArray();
    $expectedDatosUI['session']['comissions'] = $comissions;
    $expectedDatosUI['breadcrumb']            = 'Comisiones';
    $expectedDatosUI['message']               = ['La comisión se eliminó exitosamente'];

    $template = 'comissions.html.twig';

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