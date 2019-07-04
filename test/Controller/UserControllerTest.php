<?php

use PHPUnit\Framework\TestCase;
use \Solcre\lmsuy\Service\UserService;
use \Solcre\lmsuy\Entity\UserEntity;
use Solcre\lmsuy\Exception\UserHadActionException;
use Solcre\lmsuy\Controller\UserController;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;
use Test\AppWrapper;

class UserControllerTest extends TestCase
{

  public function createController($view, $userService) {
      $container = AppWrapper::getContainer();

      // Get EntityManager from container
      $entityManager = $container->get(EntityManager::class);

      $viewMock = 
      $controller = new UserController($view, $entityManager);

      // Inject the mocked comissionService by reflection
      $reflection = new ReflectionProperty($controller, 'userService');
      $reflection->setAccessible(true);
      $reflection->setValue($controller, $userService);

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

  public function testListAll()
  {
    $view = $this->createMock(Slim\Views\Twig::class);
    $userService = $this->createMock(UserService::class);

    $expectedUsers = $this->getAListOfUsers();

    $userService->method('fetchAll')->willReturn($expectedUsers);

    $controller = $this->createController($view, $userService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [];

    $expectedDatosUI = [];

    foreach ($expectedUsers as $user) {
      $users[] = $user->toArray();
    }

    $expectedDatosUI['users']      = $users;
    $expectedDatosUI['breadcrumb'] = 'Usuarios';

    $template    = 'viewUsers.html.twig';

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
    $userService = $this->createMock(UserService::class);

    $expectedUser = new UserEntity(
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
    );

    $userService->method('fetchOne')->willReturn($expectedUser);

    $controller = $this->createController($view, $userService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'iduser' => 1
    ];

    $expectedDatosUI = [];

    $expectedDatosUI['user']               = $expectedUser->toArray();
    $expectedDatosUI['breadcrumb']            = 'Editar Usuario';

    $template    = 'editPlayer.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->list($request, $response, $args);
  }

  public function testAdd() //WhenIsAdded
  {

    $view = $this->createMock(Slim\Views\Twig::class);
    $userService = $this->createMock(UserService::class);

    $expectedUsers = $this->getAListOfUsers();

    $userService->method('fetchAll')->willReturn($expectedUsers);
    $userService->method('add')->willReturn(true);

    $controller = $this->createController($view, $userService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      [
        'id'         => 1,
        'password'   => 123,
        'mobile'     => 1234,
        'email'      => 'diego@lmsuy.com',
        'lastname'   => 'Rod',
        'name'       => 'Diego',
        'username'   => 12345,
        'multiplier' => 0,
        'isActive'   => 1,
        'hours'      => 0,
        'points'     => 0,
        'sessions'   => 0,
        'results'    => 0,
        'cashin'     => 0
      ]
    );

    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [];

    $expectedDatosUI = [];

    foreach ($expectedUsers as $user) {
      $users[] = $user->toArray();
    }

    $expectedDatosUI['users']      = $users;
    $expectedDatosUI['breadcrumb'] = 'Usuarios';
    $expectedDatosUI['message']    = ['El usuario se agregó exitosamente.'];

    $template    = 'viewUsers.html.twig';

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
    $userService = $this->createMock(UserService::class);

    $controller = $this->createController($view, $userService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [];

    $expectedDatosUI = [];

    $expectedDatosUI['breadcrumb'] = 'Nuevo Usuario';

    $template    = 'addUser.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->form($request, $response, $args);
  }

  public function testUpdate() //WhenIsAdded
  {

    $view = $this->createMock(Slim\Views\Twig::class);
    $userService = $this->createMock(UserService::class);

    $expectedUsers = $this->getAListOfUsers();

    $userService->method('fetchAll')->willReturn($expectedUsers);
    $userService->method('update')->willReturn(true);

    $controller = $this->createController($view, $userService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      [
        'id'         => 1,
        'password'   => 123,
        'mobile'     => 1234,
        'email'      => 'diego@lmsuy.com',
        'lastname'   => 'Rod',
        'name'       => 'Diego',
        'username'   => 12345,
        'multiplier' => 0,
        'isActive'   => 1,
        'hours'      => 0,
        'points'     => 0,
        'sessions'   => 0,
        'results'    => 0,
        'cashin'     => 0
      ]
    );

    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [];

    $expectedDatosUI = [];

    foreach ($expectedUsers as $user) {
      $users[] = $user->toArray();
    }

    $expectedDatosUI['users']      = $users;
    $expectedDatosUI['breadcrumb'] = 'Usuarios';
    $expectedDatosUI['message']    = ['El usuario se actualizó exitosamente'];

    $template    = 'viewUsers.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->update($request, $response, $args);

  }

  public function testDelete() //WhenIsAdded
  {

    $view = $this->createMock(Slim\Views\Twig::class);
    $userService = $this->createMock(UserService::class);

    $expectedUsers = $this->getAListOfUsers();

    $userService->method('fetchAll')->willReturn($expectedUsers);
    $userService->method('delete')->willReturn(true);

    $controller = $this->createController($view, $userService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      [
        'id'         => 1,
        'password'   => 123,
        'mobile'     => 1234,
        'email'      => 'diego@lmsuy.com',
        'lastname'   => 'Rod',
        'name'       => 'Diego',
        'username'   => 12345,
        'multiplier' => 0,
        'isActive'   => 1,
        'hours'      => 0,
        'points'     => 0,
        'sessions'   => 0,
        'results'    => 0,
        'cashin'     => 0
      ]
    );

    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'iduser' => 1
    ];

    $expectedDatosUI = [];

    foreach ($expectedUsers as $user) {
      $users[] = $user->toArray();
    }

    $expectedDatosUI['users']      = $users;
    $expectedDatosUI['breadcrumb'] = 'Usuarios';
    $expectedDatosUI['message']    = ['El usuario se eliminó exitosamente'];

    $template    = 'viewUsers.html.twig';

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