<?php

use PHPUnit\Framework\TestCase;
use \Solcre\Pokerclub\Service\ExpensesSessionService;
use \Solcre\Pokerclub\Service\SessionService;
use \Solcre\Pokerclub\Entity\ExpensesSessionEntity;
use \Solcre\Pokerclub\Entity\SessionEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;
use \Solcre\Pokerclub\Exception\ExpensesInvalidException;
use Test\AppWrapper;
use Solcre\lmsuy\Controller\ExpensesSessionController;

class ExpensesSessionControllerTest extends TestCase
{

  public function createController($view, $expensesService, $sessionService) {
      $container = AppWrapper::getContainer();

      // Get EntityManager from container
      $entityManager = $container->get(EntityManager::class);

      $controller = new ExpensesSessionController($view, $entityManager);

      // Inject the mocked expensesService by reflection
      $reflection = new ReflectionProperty($controller, 'expensesService');
      $reflection->setAccessible(true);
      $reflection->setValue($controller, $expensesService);

      // Inject the mocked sessionService by reflection
      $reflection = new ReflectionProperty($controller, 'sessionService');
      $reflection->setAccessible(true);
      $reflection->setValue($controller, $sessionService);

      return $controller;
  }

  public function getAListOfExpenses($session)
  {
    return [ 
      new ExpensesSessionEntity(
        1,
        $session,
        'gasto1',
        100
      ),
      new ExpensesSessionEntity(
        2,
        $session,
        'gasto2',
        200
      ),
    ];
  }

  public function testListAll()
  {
    $view = $this->createMock(Slim\Views\Twig::class);
    $expensesService = $this->createMock(ExpensesSessionService::class);
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

    $expectedExpenses = $this->getAListOfExpenses($expectedSession);

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $expensesService->method('fetchAll')->willReturn($expectedExpenses);

    $controller = $this->createController($view, $expensesService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession' => 2
    ];

    $expectedDatosUI = [];
    foreach ($expectedExpenses as $expenditure) {
      $expenses[] = $expenditure->toArray();
    }

    $expectedDatosUI['session']           = $expectedSession->toArray();
    $expectedDatosUI['session']['expenses'] = $expenses;
    $expectedDatosUI['breadcrumb']                 = 'Gastos';

    $template    = 'expenses.html.twig';

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
    $expensesService = $this->createMock(ExpensesSessionService::class);
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

    $expectedExpenditure =  new ExpensesSessionEntity(
      1,
      $expectedSession,
      'gasto1',
      100
    );

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $expensesService->method('fetchOne')->willReturn($expectedExpenditure);

    $controller = $this->createController($view, $expensesService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession'     => 2,
      'idExpenditure' => 1
    ];

    $expectedDatosUI = [];

    $expectedDatosUI['session']                = $expectedSession->toArray();
    $expectedDatosUI['session']['expenditure'] = $expectedExpenditure->toArray();
    $expectedDatosUI['breadcrumb']             = 'Editar item';

    $template    = 'editExpenses.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->list($request, $response, $args);

  }

  public function testAdd()
  {

    $view = $this->createMock(Slim\Views\Twig::class);
    $expensesService = $this->createMock(ExpensesSessionService::class);
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

    $expectedExpenses = $this->getAListOfExpenses($expectedSession);

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $expensesService->method('fetchAll')->willReturn($expectedExpenses);
    $expensesService->method('add')->willReturn(true);

    $controller = $this->createController($view, $expensesService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(['expenditure to add']);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession' => 2
    ];

    $expectedDatosUI = [];

    foreach ($expectedExpenses as $expenditure) {
      $expenses[] = $expenditure->toArray();
    }

    $expectedDatosUI['session']             = $expectedSession->toArray();
    $expectedDatosUI['session']['expenses'] = $expenses;
    $expectedDatosUI['breadcrumb']          = 'Gastos de Sesión';
    $expectedDatosUI['message']             = ['el item se ingresó exitosamente.'];

    $template = 'expenses.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->add($request, $response, $args);
  }

  public function testAddWithInvalidAmount()
  {

    $view = $this->createMock(Slim\Views\Twig::class);
    $expensesService = $this->createMock(ExpensesSessionService::class);
    $sessionService = $this->createMock(SessionService::class);

    $sessionService->method('fetchOne')->willReturn(null);
    $expensesService->method('fetchAll')->willReturn(null);
    
    $exception = new ExpensesInvalidException();
    $expensesService->method('add')->will($this->throwException($exception));

    $controller = $this->createController($view, $expensesService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(['expenditure to add']);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession' => 2
    ];

    $expectedDatosUI = [];

    $template = 'expenses.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->contains([$exception->getMessage()]),
    );

    $controller->add($request, $response, $args);
  }

  public function testForm()
  {
    $view = $this->createMock(Slim\Views\Twig::class);
    $expensesService = $this->createMock(ExpensesSessionService::class);
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

    $controller = $this->createController($view, $expensesService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession'  => 2
    ];

    $expectedDatosUI = [];

    $expectedDatosUI['session']               = $expectedSession->toArray();
    $expectedDatosUI['breadcrumb']            = 'Nuevo item';

    $template    = 'newexpenses.html.twig';

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
    $expensesService = $this->createMock(ExpensesSessionService::class);
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

    $expectedExpenses = $this->getAListOfExpenses($expectedSession);

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $expensesService->method('fetchAll')->willReturn($expectedExpenses);
    $expensesService->method('update')->willReturn(true);

    $controller = $this->createController($view, $expensesService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      ['idSession' => 1
      ]
    );
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession'     => 2,
      'idExpenditure' => 1
    ];

    $expectedDatosUI = [];

    foreach ($expectedExpenses as $expenditure) {
      $expenses[] = $expenditure->toArray();
    }

    $expectedDatosUI['session']             = $expectedSession->toArray();
    $expectedDatosUI['session']['expenses'] = $expenses;
    $expectedDatosUI['breadcrumb']          = 'Gastos de Sesión';
    $expectedDatosUI['message']             = ['El item se actualizó exitosamente'];

    $template = 'expenses.html.twig';

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
    $expensesService = $this->createMock(ExpensesSessionService::class);
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

    $expectedExpenses = $this->getAListOfExpenses($expectedSession);

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $expensesService->method('fetchAll')->willReturn($expectedExpenses);
    $expensesService->method('delete')->willReturn(true);

    $controller = $this->createController($view, $expensesService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      ['idSession' => 1
      ]
    );
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession'     => 2,
      'idExpenditure' => 1
    ];

    $expectedDatosUI = [];

    foreach ($expectedExpenses as $expenditure) {
      $expenses[] = $expenditure->toArray();
    }

    $expectedDatosUI['session']             = $expectedSession->toArray();
    $expectedDatosUI['session']['expenses'] = $expenses;
    $expectedDatosUI['breadcrumb']          = 'Gastos de Sesión';
    $expectedDatosUI['message']             = ['El item se eliminó exitosamente'];

    $template = 'expenses.html.twig';

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