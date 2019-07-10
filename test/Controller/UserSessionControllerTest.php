<?php

use PHPUnit\Framework\TestCase;
use \Solcre\lmsuy\Service\UserSessionService;
use \Solcre\lmsuy\Service\SessionService;
use \Solcre\lmsuy\Service\UserService;
use \Solcre\lmsuy\Entity\UserSessionEntity;
use \Solcre\lmsuy\Entity\SessionEntity;
use \Solcre\lmsuy\Entity\UserEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;
use Solcre\lmsuy\Controller\UserSessionController;
use Test\AppWrapper;
use Solcre\lmsuy\Exception\TableIsFullException;
use Solcre\lmsuy\Exception\UserSessionAlreadyAddedException;

class UserSessionControllerTest extends TestCase
{

  public function createController($view, $userSessionService, $userService, $sessionService) {
      $container = AppWrapper::getContainer();

      // Get EntityManager from container
      $entityManager = $container->get(EntityManager::class);

      $viewMock = 
      $controller = new UserSessionController($view, $entityManager);

      // Inject the mocked userSessionService by reflection
      $reflection = new ReflectionProperty($controller, 'userSessionService');
      $reflection->setAccessible(true);
      $reflection->setValue($controller, $userSessionService);

      // Inject the mocked userService by reflection
      $reflection = new ReflectionProperty($controller, 'userService');
      $reflection->setAccessible(true);
      $reflection->setValue($controller, $userService);

      // Inject the mocked sessionService by reflection
      $reflection = new ReflectionProperty($controller, 'sessionService');
      $reflection->setAccessible(true);
      $reflection->setValue($controller, $sessionService);

      return $controller;
  }
  public function getAListOfUsers()
  {
    return [ 
      new UserEntity(
        1,
        123,
        1234,
        'diego@lmsuy.com',
        'Rod',
        'Diego',
        12345,
        0,
        1,
        0,
        0,
        0,
        0,
        0
      ),
      new UserEntity(
        2,
        223,
        2345,
        'matias@lmsuy.com',
        'Fuster',
        'Matias',
        123456,
        0,
        1,
        0,
        0,
        0,
        0,
        0
      )
    ];
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

  public function testListAll()
  {
    $view = $this->createMock(Slim\Views\Twig::class);
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

    $controller = $this->createController($view, $userSessionService, $userService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession' => 2
    ];

    $expectedDatosUI = [];

    foreach ($expectedUsersSession as $userSession) {
      $usersSession[] = $userSession->toArray();
    }

    $expectedDatosUI['session']                 = $expectedSession->toArray();
    $expectedDatosUI['session']['usersSession'] = $usersSession;
    $expectedDatosUI['breadcrumb']              = 'Usuarios de Sesion';

    $template    = 'users.html.twig';

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

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $userSessionService->method('fetchOne')->willReturn($expectedUserSession);

    $controller = $this->createController($view, $userSessionService, $userService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession'  => 2,
      'idusersession' => 1
    ];

    $expectedDatosUI = [];

    // $expectedDatosUI['session']               = $expectedSession->toArray();
    $expectedDatosUI['userSession'] = $expectedUserSession->toArray();
    $expectedDatosUI['breadcrumb']  = 'Editar Usuario';

    $template    = 'editUser.html.twig';

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
    $userSessionService->method('add')->willReturn(true);

    $controller = $this->createController($view, $userSessionService, $userService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      [
        'userSession to add',
        'idSession'         => 2,
        'user_id'           => [1],
        'start'             => date_create('2019-06-27 19:00:00'),
        'end'               => date_create('2019-06-27 23:00:00'),
        'approved'          => 1,
        'accumulatedPoints' => 0,
        'idSession'         => 2
      ]
    );

    $response = $this->createMock(Slim\Psr7\Response::class);

    $args = [
      'idSession' => 2
    ];

    $expectedDatosUI = [];

    foreach ($expectedUsersSession as $userSession) {
      $usersSession[] = $userSession->toArray();
    }

    $expectedDatosUI['session']                 = $expectedSession->toArray();
    $expectedDatosUI['session']['usersSession'] = $usersSession;
    $expectedDatosUI['breadcrumb']              = 'Usuarios de Sesión';
    $expectedDatosUI['message']                 = ['Se agregó exitosamente.'];

    $template    = 'users.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->add($request, $response, $args);
  }

  public function testAddWhenTableIsFull() 
  {
    $view = $this->createMock(Slim\Views\Twig::class);
    $userSessionService = $this->createMock(UserSessionService::class);
    $userService = $this->createMock(UserService::class);
    $sessionService = $this->createMock(SessionService::class);


    $sessionService->method('fetchOne')->willReturn(null);
    $userSessionService->method('fetchAll')->willReturn(null);
    $exception = new TableIsFullException();
    $userSessionService->method('add')->will($this->throwException($exception));

    $controller = $this->createController($view, $userSessionService, $userService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      [
        'userSession to add',
        'idSession'         => 2,
        'user_id'           => [1],
        'start'             => date_create('2019-06-27 19:00:00'),
        'end'               => date_create('2019-06-27 23:00:00'),
        'approved'          => 1,
        'accumulatedPoints' => 0,
        'idSession'         => 2
      ]
    );

    $response = $this->createMock(Slim\Psr7\Response::class);

    $args = [
      'idSession' => 2
    ];

    $template    = 'users.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->contains([$exception->getMessage()]),
    );

    $controller->add($request, $response, $args);
  }

  public function testAddAlreadedAdded() 
  {
    $view = $this->createMock(Slim\Views\Twig::class);
    $userSessionService = $this->createMock(UserSessionService::class);
    $userService = $this->createMock(UserService::class);
    $sessionService = $this->createMock(SessionService::class);


    $sessionService->method('fetchOne')->willReturn(null);
    $userSessionService->method('fetchAll')->willReturn(null);
    $exception = new UserSessionAlreadyAddedException();
    $userSessionService->method('add')->will($this->throwException($exception));

    $controller = $this->createController($view, $userSessionService, $userService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      [
        'userSession to add',
        'idSession'         => 2,
        'user_id'           => [1],
        'start'             => date_create('2019-06-27 19:00:00'),
        'end'               => date_create('2019-06-27 23:00:00'),
        'approved'          => 1,
        'accumulatedPoints' => 0,
        'idSession'         => 2
      ]
    );

    $response = $this->createMock(Slim\Psr7\Response::class);

    $args = [
      'idSession' => 2
    ];

    $template    = 'users.html.twig';

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
  
    $expectedUsers = $this->getAListOfUsers();

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $userService->method('fetchAll')->willReturn($expectedUsers);

    $controller = $this->createController($view, $userSessionService, $userService, $sessionService);
    $request    = $this->createMock(Slim\Psr7\Request::class);
    $response   = $this->createMock(Slim\Psr7\Response::class);

    $args = [
      'idSession'  => 2
    ];

    $expectedDatosUI = [];

    foreach ($expectedUsers as $user) {
      $expectedUsersArray[] = $user->toArray();
    }
    
    $expectedDatosUI['session']    = $expectedSession->toArray();
    $expectedDatosUI['users']      = $expectedUsersArray;
    $expectedDatosUI['breadcrumb'] = 'Nuevo UserSession';

    $template    = 'newusers.html.twig';

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
    $userSessionService->method('update')->willReturn(true);

    $controller = $this->createController($view, $userSessionService, $userService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getParsedBody')->willReturn(
          [
            'userSession to add',
            'idSession'         => 2,
            'user_id'           => [1],
            'start'             => date_create('2019-06-27 19:00:00'),
            'end'               => date_create('2019-06-27 23:00:00'),
            'approved'          => 1,
            'accumulatedPoints' => 0,
            'idSession'         => 2
          ]
        );

    $response = $this->createMock(Slim\Psr7\Response::class);

    $args = [
      'idSession' => 2
    ];

    $expectedDatosUI = [];

    foreach ($expectedUsersSession as $userSession) {
      $usersSession[] = $userSession->toArray();
    }

    $expectedDatosUI['session']                 = $expectedSession->toArray();
    $expectedDatosUI['session']['usersSession'] = $usersSession;
    $expectedDatosUI['breadcrumb']              = 'Usuarios de Sesión';
    $expectedDatosUI['message']                 = ['El usuario se actualizó exitosamente'];

    $template    = 'users.html.twig';

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
    $userSessionService->method('delete')->willReturn(true);

    $controller = $this->createController($view, $userSessionService, $userService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getParsedBody')->willReturn(
          [
            'userSession to add',
            'idSession'         => 2,
            'user_id'           => [1],
            'start'             => date_create('2019-06-27 19:00:00'),
            'end'               => date_create('2019-06-27 23:00:00'),
            'approved'          => 1,
            'accumulatedPoints' => 0,
            'idSession'         => 2
          ]
        );

    $response = $this->createMock(Slim\Psr7\Response::class);

    $args = [
      'idSession'     => 2,
      'idusersession' =>1
    ];

    $expectedDatosUI = [];

    foreach ($expectedUsersSession as $userSession) {
      $usersSession[] = $userSession->toArray();
    }

    $expectedDatosUI['session']                 = $expectedSession->toArray();
    $expectedDatosUI['session']['usersSession'] = $usersSession;
    $expectedDatosUI['breadcrumb']              = 'Usuarios de Sesión';
    $expectedDatosUI['message']                 = ['El usuario se eliminó exitosamente de la sesión'];

    $template    = 'users.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->delete($request, $response, $args);
  }

  public function testFormClose()
  {
    $view = $this->createMock(Slim\Views\Twig::class);
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

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $userSessionService->method('fetchOne')->willReturn($expectedUserSession);

    $controller = $this->createController($view, $userSessionService, $userService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession'  => 2,
      'idusersession' => 1
    ];

    $expectedDatosUI = [];

    // $expectedDatosUI['session']               = $expectedSession->toArray();
    $expectedDatosUI['userSession'] = $expectedUserSession->toArray();
    $expectedDatosUI['breadcrumb']  = 'Cerrar Session de Usuario';

    $template    = 'closeUserSession.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->formClose($request, $response, $args);

  }

  public function testClose()
  {
    $view = $this->createMock(Slim\Views\Twig::class);
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
    $userSessionService->method('fetchOne')->willReturn($expectedUsersSession[0]);
    $userSessionService->method('close')->willReturn(true);

    $controller = $this->createController($view, $userSessionService, $userService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession'     => 2,
      'idusersession' => 1
    ];

    $expectedDatosUI = [];

    foreach ($expectedUsersSession as $userSession) {
      $usersSession[] = $userSession->toArray();
    }

    $expectedDatosUI['session']                 = $expectedSession->toArray();
    $expectedDatosUI['session']['usersSession'] = $usersSession;
    $expectedDatosUI['breadcrumb']              = 'Usuarios de Sesión';
    $expectedDatosUI['message']                 = ['El usuario ha salido de la sesión'];

    $template    = 'users.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->close($request, $response, $args);

  }

}