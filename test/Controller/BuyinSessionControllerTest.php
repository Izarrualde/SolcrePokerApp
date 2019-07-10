<?php

use PHPUnit\Framework\TestCase;
use \Solcre\lmsuy\Service\BuyinSessionService;
use \Solcre\lmsuy\Service\SessionService;
use \Solcre\lmsuy\Service\UserSessionService;
use \Solcre\lmsuy\Service\UserService;
use \Solcre\lmsuy\Entity\BuyinSessionEntity;
use \Solcre\lmsuy\Entity\SessionEntity;
use \Solcre\lmsuy\Entity\UserSessionEntity;
use \Solcre\lmsuy\Entity\UserEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;
use Solcre\lmsuy\Exception\BuyinInvalidException;
use Test\AppWrapper;
use Solcre\lmsuy\Controller\BuyinSessionController;

class BuyinSessionControllerTest extends TestCase
{

  public function createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService) {
      $container = AppWrapper::getContainer();

      // Get EntityManager from container
      $entityManager = $container->get(EntityManager::class);

      $viewMock = 
      $controller = new BuyinSessionController($view, $entityManager);

      // Inject the mocked buyinSessionService by reflection
      $reflection = new ReflectionProperty($controller, 'buyinSessionService');
      $reflection->setAccessible(true);
      $reflection->setValue($controller, $buyinSessionService);

      $reflection = new ReflectionProperty($controller, 'userSessionService');
      $reflection->setAccessible(true);
      $reflection->setValue($controller, $userSessionService);

      $reflection = new ReflectionProperty($controller, 'userService');
      $reflection->setAccessible(true);
      $reflection->setValue($controller, $userService);

      $reflection = new ReflectionProperty($controller, 'sessionService');
      $reflection->setAccessible(true);
      $reflection->setValue($controller, $sessionService);

      return $controller;
  }

    public function getAListOfUsersSession($session)
  {
    $user1 = New UserEntity();
    $user2 = New UserEntity();

    $userSession1 = new UserSessionEntity(
      1,
      $session,
      1,
      1,
      0,
      0,
      null,
      null,
      $user1
    );

    $userSession2 = new UserSessionEntity(
      2,
      $session,
      1,
      1,
      0,
      0,
      null,
      null,
      $user2
    );

    return [$userSession1, $userSession2];
}

  public function getAListOfBuyins($session)
  {
    $user1 = New UserEntity();
    $user2 = New UserEntity();

    $userSession1 = new UserSessionEntity(
      1,
      $session,
      1,
      1,
      0,
      0,
      null,
      null,
      $user1
    );

    $userSession2 = new UserSessionEntity(
      2,
      $session,
      1,
      1,
      0,
      0,
      null,
      null,
      $user2
    );

    return [ 
      new BuyinSessionEntity(
        1,
        100,
        200,
        $userSession1,
        date_create('2019-06-26 19:00:00'),
        1,
        1
      ),
      new BuyinSessionEntity(
        2,
        200,
        300,
        $userSession2,
        date_create('2019-06-26 19:05:00'),
        1,
        1
      )
    ];
  }
  
  public function testListAll()
  {
    $view = $this->createMock(Slim\Views\Twig::class);
    $buyinSessionService = $this->createMock(BuyinSessionService::class);
    $userSessionService = $this->createMock(UserSessionService::class);
    $userService = $this->createMock(UserService::class);
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

    $expectedBuyins = $this->getAListOfBuyins($expectedSession);

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $buyinSessionService->method('fetchAllBuyins')->willReturn($expectedBuyins);

    $controller = $this->createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession' => 2
    ];

    $expectedDatosUI = [];

    foreach ($expectedBuyins as $buyin) {
      $buyins[] = $buyin->toArray();
    }

    $expectedDatosUI['sessions']           = $expectedSession->toArray();
    $expectedDatosUI['sessions']['buyins'] = $buyins;
    $expectedDatosUI['breadcrumb']                 = 'Buyins';

    $template    = 'buyins.html.twig';

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
    $buyinSessionService = $this->createMock(BuyinSessionService::class);
    $userSessionService = $this->createMock(UserSessionService::class);
    $userService = $this->createMock(UserService::class);
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

    $user1 = New UserEntity();

    $expectedUserSession = new UserSessionEntity(
      2,
      $expectedSession,
      1,
      1,
      0,
      0,
      null,
      null,
      $user1
    );

    $expectedBuyin = new BuyinSessionEntity(
      2,
      200,
      300,
      $expectedUserSession,
      date_create('2019-06-26 19:05:00'),
      1,
      1
    );

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $buyinSessionService->method('fetchOne')->willReturn($expectedBuyin);

    $controller = $this->createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession'  => 2,
      'idbuyin' => 1
    ];

    $expectedDatosUI = [];

    // $expectedDatosUI['session']               = $expectedSession->toArray();
    $expectedDatosUI['buyin'] = $expectedBuyin->toArray();
    $expectedDatosUI['breadcrumb']  = 'Editar Buyin';

    $template    = 'editBuyin.html.twig';

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
    $buyinSessionService = $this->createMock(BuyinSessionService::class);
    $userSessionService = $this->createMock(UserSessionService::class);
    $userService = $this->createMock(UserService::class);
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

    $expectedBuyins = $this->getAListOfBuyins($expectedSession);

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $buyinSessionService->method('fetchAllBuyins')->willReturn($expectedBuyins);
    $buyinSessionService->method('add')->willReturn(true);

    $controller = $this->createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      [
        'buyin to add',
        'idSession' => 2
      ]
    );
    $response = $this->createMock(Slim\Psr7\Response::class);

    $args = [
      'idSession' => 2
    ];

    $expectedDatosUI = [];

    foreach ($expectedBuyins as $buyin) {
      $buyins[] = $buyin->toArray();
    }

    $expectedDatosUI['session']           = $expectedSession->toArray();
    $expectedDatosUI['session']['buyins'] = $buyins;
    $expectedDatosUI['breadcrumb']         = 'Buyins';
    $expectedDatosUI['message']            = ['El buyin se agregó exitosamente'];

    $template    = 'buyins.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->add($request, $response, $args);
  }

  public function testAddWithInvalidBuyin()
  {
    $view = $this->createMock(Slim\Views\Twig::class);
    $buyinSessionService = $this->createMock(BuyinSessionService::class);
    $userSessionService = $this->createMock(UserSessionService::class);
    $userService = $this->createMock(UserService::class);
    $sessionService = $this->createMock(SessionService::class);

    $sessionService->method('fetchOne')->willReturn(null);
    $buyinSessionService->method('fetchAllBuyins')->willReturn(null);
    $exception = new BuyinInvalidException();
    $buyinSessionService->method('add')->will($this->throwException($exception));

    $controller = $this->createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      [
        'buyin to add',
        'idSession' => 2
      ]
    );
    $response = $this->createMock(Slim\Psr7\Response::class);

    $args = [
      'idSession' => 2
    ];

    $template    = 'buyins.html.twig';

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
    $buyinSessionService = $this->createMock(BuyinSessionService::class);
    $userSessionService = $this->createMock(UserSessionService::class);
    $userService = $this->createMock(UserService::class);
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
    $expectedUsersSession = $this->getAListOfUsersSession($expectedSession);
    
    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $userSessionService->method('fetchAll')->willReturn($expectedUsersSession);

    $controller = $this->createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession'  => 2
    ];

    $expectedDatosUI = [];
    foreach ($expectedUsersSession as $userSession) {
      $expectedUsersSessionArray[] = $userSession->toArray();
    }

    $expectedDatosUI['session']               = $expectedSession->toArray();
    $expectedDatosUI['session']['usersSession'] = $expectedUsersSessionArray;
    $expectedDatosUI['breadcrumb']            = 'Nuevo Buyin';

    $template    = 'newbuyins.html.twig';

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
    $buyinSessionService = $this->createMock(BuyinSessionService::class);
    $userSessionService = $this->createMock(UserSessionService::class);
    $userService = $this->createMock(UserService::class);
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

    $expectedBuyins = $this->getAListOfBuyins($expectedSession);

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $buyinSessionService->method('fetchAllBuyins')->willReturn($expectedBuyins);
    $buyinSessionService->method('update')->willReturn(true);

    $controller = $this->createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      [
        'buyin to add',
        'idSession' => 2
      ]
    );
    $response = $this->createMock(Slim\Psr7\Response::class);

    $args = [
      'idSession' => 2
    ];

    $expectedDatosUI = [];

    foreach ($expectedBuyins as $buyin) {
      $buyins[] = $buyin->toArray();
    }

    $expectedDatosUI['session']           = $expectedSession->toArray();
    $expectedDatosUI['session']['buyins'] = $buyins;
    $expectedDatosUI['breadcrumb']         = 'Buyins';
    $expectedDatosUI['message']            = ['El buyin se actualizó exitosamente'];

    $template    = 'buyins.html.twig';

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
    $buyinSessionService = $this->createMock(BuyinSessionService::class);
    $userSessionService = $this->createMock(UserSessionService::class);
    $userService = $this->createMock(UserService::class);
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

    $expectedBuyins = $this->getAListOfBuyins($expectedSession);

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $buyinSessionService->method('fetchAllBuyins')->willReturn($expectedBuyins);
    $buyinSessionService->method('delete')->willReturn(true);

    $controller = $this->createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      [
        'buyin to add',
        'idSession' => 2
      ]
    );
    $response = $this->createMock(Slim\Psr7\Response::class);

    $args = [
      'idSession' => 2,
      'idbuyin'   => 1
    ];

    $expectedDatosUI = [];

    foreach ($expectedBuyins as $buyin) {
      $buyins[] = $buyin->toArray();
    }

    $expectedDatosUI['session']           = $expectedSession->toArray();
    $expectedDatosUI['session']['buyins'] = $buyins;
    $expectedDatosUI['breadcrumb']         = 'Buyins';
    $expectedDatosUI['message']            = ['El buyin se eliminó exitosamente'];

    $template    = 'buyins.html.twig';

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