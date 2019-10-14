<?php

use PHPUnit\Framework\TestCase;
use \Solcre\Pokerclub\Service\UserService;
use \Solcre\Pokerclub\Entity\UserEntity;
use Solcre\Pokerclub\Exception\UserHadActionException;
use Solcre\lmsuy\Controller\UserController;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;
use Test\AppWrapper;
use Solcre\Pokerclub\Exception\UserNotFoundException;
use Solcre\Pokerclub\Exception\UserInvalidException;

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

    public function listAllSetup($view)
    {
        $userService = $this->createMock(UserService::class);

        $expectedUsers = $this->getAListOfUsers();

        $userService->method('fetchAll')->willReturn($expectedUsers);

        $controller = $this->createController($view, $userService);

        $expectedDatosUI = [];

        foreach ($expectedUsers as $user) {
          $expectedUsersArray[] = $user->toArray();
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = $expectedUsersArray;
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

        $userService->method('fetch')->willReturn($expectedUser);

        $controller = $this->createController($view, $userService);

        $expectedDatosUI = [];

        if ($view instanceof JsonView) {
            $expectedDatosUI = $expectedUser->toArray();
        }

        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI 
        ];
    }

    public function listWithExceptionSetup($view, $exception)
    {
        $request          = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $userService = $this->createMock(UserService::class);

        $userService->method('fetch')->will($this->throwException($exception));

        $controller = $this->createController($view, $userService);

        $expectedDatosUI = [];

        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI, 
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
          'iduser' => 1
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

    public function testListUserNotFoundWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'iduser' => 1
        ];

        $exception = new UserNotFoundException();

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

    public function testListUserWithExceptionWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'iduser' => 1
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\UserEntity' . " Entity not found", 404);

        $setup            = $this->listWithExceptionSetup($view, $exception);
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

        $controller->list($request, $response, $args);
    }

    public function addSetup($view)
    {
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

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $userService = $this->createMock(UserService::class);

        $post = $request->getParsedBody();
        
        $expectedUserAdded = new UserEntity(
            $post['id'],
            $post['password'],
            $post['mobile'],
            $post['email'],
            $post['lastname'],
            $post['name'],
            $post['username'],
            $post['multiplier'],
            $post['isActive'],
            $post['hours'],
            $post['points'],
            $post['sessions'],
            $post['results'],
            $post['cashin']
        );

        $expectedUsers = [
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
          ),
          $expectedUserAdded
        ];

        $userService->method('fetchAll')->willReturn($expectedUsers);
        $userService->method('add')->willReturn($expectedUserAdded);

        $controller = $this->createController($view, $userService);

        $expectedDatosUI = [];

        foreach ($expectedUsers as $user) {
          $expectedUsersArray[] = $user->toArray();
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = $expectedUserAdded->toArray();
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

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $userService = $this->createMock(UserService::class);

        $post = $request->getParsedBody();

        $expectedUsers = $this->getAListOfUsers();

        $userService->method('fetchAll')->willReturn($expectedUsers);
        $userService->method('add')->will($this->throwException($exception));
        $userService->method('update')->will($this->throwException($exception));

        $controller = $this->createController($view, $userService);

        $expectedDatosUI = [];

        foreach ($expectedUsers as $user) {
          $expectedUsersArray[] = $user->toArray();
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

        $setup           = $this->addSetup($view);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];
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

    public function testAddUserInvalidWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [];

        $exception = new UserInvalidException();

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

    public function testAddUserWithExceptionWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [];

        $exception = new Exception('Solcre\Pokerclub\Entity\UserSessionEntity' . " Entity not found", 404); 


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

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $post = $request->getParsedBody();

        $userService = $this->createMock(UserService::class);

        $expectedUserUpdated = new UserEntity(
            $post['id'],
            $post['password'],
            $post['mobile'],
            $post['email'],
            $post['lastname'],
            $post['name'],
            $post['username'],
            $post['multiplier'],
            $post['isActive'],
            $post['hours'],
            $post['points'],
            $post['sessions'],
            $post['results'],
            $post['cashin']
        );

        $expectedUsers = [
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
          ),
          $expectedUserUpdated
        ];


        $userService->method('fetchAll')->willReturn($expectedUsers);
        $userService->method('update')->willReturn($expectedUserUpdated);

        $controller = $this->createController($view, $userService);

        $expectedDatosUI = [];

        foreach ($expectedUsers as $user) {
          $expectedUsersArray[] = $user->toArray();
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI  = $expectedUserUpdated->toArray();
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

    public function testUpdateUserInvalidWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [];

        $exception = new UserInvalidException();

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

    public function testUpdateUserNotFoundWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [];

        $exception = new UserNotFoundException();

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
            $this->equalTo($expectedDatosUI),
        );

        $controller->update($request, $response, $args);
    }

    public function testUpdateWithExceptionWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [];

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

        $userService = $this->createMock(UserService::class);

        $expectedUsers = $this->getAListOfUsers();

        $userService->method('fetchAll')->willReturn($expectedUsers);
        $userService->method('delete')->willReturn(true);

        $controller = $this->createController($view, $userService);

        $expectedDatosUI = null;

        foreach ($expectedUsers as $user) {
          $expectedUsersArray[] = $user->toArray();
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

        $userService = $this->createMock(USerService::class);

        $expectedUsers = $this->getAListOfUsers();

        $userService->method('fetchAll')->willReturn($expectedUsers);
        $userService->method('delete')->will($this->throwException($exception));

        $controller = $this->createController($view, $userService);

        $expectedDatosUI = null;

        foreach ($expectedUsers as $user) {
          $expectedUsersArray[] = $user->toArray();
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

    public function testDeleteWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'iduser' => 1
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

    public function testDeleteUserNotFoundJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'iduser' => 1
        ];

        $exception = new UserNotFoundException();

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
          'iduser' => 1
        ];

        $exception = new \Exception('Solcre\Pokerclub\Entity\UserEntity' . " Entity not found", 404); 

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
}
