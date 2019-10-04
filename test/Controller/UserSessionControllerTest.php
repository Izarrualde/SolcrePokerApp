<?php

use PHPUnit\Framework\TestCase;
use \Solcre\Pokerclub\Service\UserSessionService;
use \Solcre\Pokerclub\Service\SessionService;
use \Solcre\Pokerclub\Service\UserService;
use \Solcre\Pokerclub\Entity\UserSessionEntity;
use \Solcre\Pokerclub\Entity\SessionEntity;
use \Solcre\Pokerclub\Entity\UserEntity;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\View\TwigWrapperView;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;
use Solcre\lmsuy\Controller\UserSessionController;
use Test\AppWrapper;
use Solcre\Pokerclub\Exception\TableIsFullException;
use Solcre\Pokerclub\Exception\UserSessionAlreadyAddedException;
use Solcre\Pokerclub\Exception\UserSessionNotFoundException;

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

    public function listAllSetup($view)
    {
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

        $sessionService->method('fetch')->willReturn($expectedSession);
        $userSessionService->method('fetchAll')->willReturn($expectedUsersSession);

        $controller = $this->createController($view, $userSessionService, $userService, $sessionService);
        $expectedDatosUI = [];

        foreach ($expectedUsersSession as $userSession) {
          $expectedUsersSessionArray[] = $userSession->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']                 = $expectedSession->toArray();
            $expectedDatosUI['session']['usersSession'] = $expectedUsersSessionArray;
            $expectedDatosUI['breadcrumb']              = 'Usuarios de Sesion';
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = $expectedUsersSessionArray;
        }

        return [ 
          'controller'      => $controller, 
          'expectedDatosUI' => $expectedDatosUI 
        ];
    }

    public function testListAll()
    {
        $view = $this->createMock(TwigWrapperView::class);
        $request  = $this->createMock(Slim\Psr7\Request::class);
        
        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $setup           = $this->listAllSetup($view);
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

    public function testListAllWithJsonView()
    {
        $view = $this->createMock(JsonView::class);
        $request  = $this->createMock(Slim\Psr7\Request::class);
        
        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response->withStatus(200);

        $setup           = $this->listAllSetup($view);
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
        $userSessionService = $this->createMock(UserSessionService::class);
        $userService = $this->createMock(UserService::class);
        $sessionService = $this->createMock(SessionService::class);

        $sessionService->method('fetch')->will($this->throwException($exception));
        $userSessionService->method('fetchAll')->willReturn(null);

        $controller = $this->createController($view, $userSessionService, $userService, $sessionService);

        $expectedDatosUI = [];

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['breadcrumb']              = 'Usuarios de Sesion';
        }

        return [ 
          'controller'      => $controller, 
          'expectedDatosUI' => $expectedDatosUI 
        ];
    }

    public function testListAllWithException()
    {
        $view = $this->createMock(TwigWrapperView::class);
        $request  = $this->createMock(Slim\Psr7\Request::class);
        
        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

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
            //$this->equalTo($expectedDatosUI),
            $this->contains([$exception->getMessage()])
        );

        $controller->listAll($request, $response, $args);
    }

      public function testListAllWithExceptionWithJsonView()
    {
        $view = $this->createMock(JsonView::class);
        $request  = $this->createMock(Slim\Psr7\Request::class);
        
        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response->withStatus(404);

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

        $sessionService->method('fetch')->willReturn($expectedSession);
        $userSessionService->method('fetch')->willReturn($expectedUserSession);

        $controller = $this->createController($view, $userSessionService, $userService, $sessionService);

        $expectedDatosUI = [];

        if ($view instanceof TwigWrapperView) {
            // $expectedDatosUI['session']               = $expectedSession->toArray();
            $expectedDatosUI['userSession'] = $expectedUserSession->toArray();
            $expectedDatosUI['breadcrumb']  = 'Editar Usuario';
        }

        // JsonView
        if ($view instanceof JsonView) {
            $expectedDatosUI  = $expectedUserSession->toArray();
        }

        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI 
        ];

    }

    public function testList()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $request          = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $args = [
          'idSession'     => 2,
          'idusersession' => 1
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

    public function testListWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $request          = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response->withStatus(200);

        $args = [
          'idSession'     => 2,
          'idusersession' => 1
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


    public function listWithExceptionSetup($view, $exception)
    {
        $userSessionService = $this->createMock(UserSessionService::class);
        $userService = $this->createMock(UserService::class);
        $sessionService = $this->createMock(SessionService::class);

        $userSessionService->method('fetch')->will($this->throwException($exception));

        $controller = $this->createController($view, $userSessionService, $userService, $sessionService);

        $expectedDatosUI = [];

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['message']    = [$exception->getMessage()];
            $expectedDatosUI['breadcrumb']  = 'Editar Usuario';
        }

        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI 
        ];
    }

    public function testListWithException()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $request          = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $args = [
          'idSession'     => 2,
          'idusersession' => 1
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\UserSessionEntity' . " Entity not found", 404);

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
        $view = $this->createMock(JsonView::class);

        $request          = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response->withStatus(404);

        $args = [
          'idSession'     => 2,
          'idusersession' => 1
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\UserSessionEntity' . " Entity not found", 404);

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

    public function addSetup($view)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getParsedBody')->willReturn(
          [
            'idSession' => 2,
            'user_id'   => [1],
            'start'     => '2019-06-27 19:00:00',
            'end'       => null,
            'approved'  => 1,
            'points'    => 0,
            'idSession' => 2
          ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;
        $userSessionService = $this->createMock(UserSessionService::class);
        $userService = $this->createMock(UserService::class);
        $sessionService = $this->createMock(SessionService::class);

        $post = $request->getParsedBody();

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

        $expectedUserSessionAdded = new UserSessionEntity(
          1,
          $expectedSession,
          $post['user_id'][0],
          $post['approved'],
          $post['points'],
          null,
          date_create($post['start']),
          null,
          $user1 = New UserEntity($post['user_id'][0])
        );

        $expectedUsersSession = [
          new UserSessionEntity(
            1,
            $expectedSession,
            2,
            1,
            0,
            null,
            date_create('2019-06-27 19:00:00'),
            null,
            $user1 = New UserEntity(2)),
          $expectedUserSessionAdded
        ];

        $sessionService->method('fetch')->willReturn($expectedSession);
        $userSessionService->method('fetchAll')->willReturn($expectedUsersSession);
        $userSessionService->method('add')->willReturn($expectedUserSessionAdded);

        $controller = $this->createController($view, $userSessionService, $userService, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedUsersSession as $userSession) {
          $expectedUsersSessionArray[] = $userSession->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']                 = $expectedSession->toArray();
            $expectedDatosUI['session']['usersSession'] = $expectedUsersSessionArray;
            $expectedDatosUI['breadcrumb']              = 'Usuarios de Sesión';
            $expectedDatosUI['message']                 = ['Se agregó exitosamente.'];
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = [$expectedUserSessionAdded->toArray()];
            $expectedResponse = $response->withStatus(201);
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function addManySetup($view)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getParsedBody')->willReturn(
          [
            'idSession' => 2,
            'user_id'   => [
                  'user1' => 1,
                  'user2' => 2,
                  'user3' => 3
            ],
            'end'       => null,
            'approved'  => 1,
            'points'    => 0,
            'idSession' => 2
          ]
        );

        $response           = new Slim\Psr7\Response();
        $expectedResponse   = $response;
        $userSessionService = $this->createMock(UserSessionService::class);
        $userService        = $this->createMock(UserService::class);
        $sessionService     = $this->createMock(SessionService::class);

        $post = $request->getParsedBody();

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

        $expectedUserAdded1 = new UserSessionEntity(
          1,
          $expectedSession,
          $post['user_id']['user1'],
          $post['approved'],
          $post['points'],
          null,
          null,
          null,
          $user1 = New UserEntity($post['user_id']['user1'])
        );

        $expectedUserAdded2 = new UserSessionEntity(
          1,
          $expectedSession,
          $post['user_id']['user2'],
          $post['approved'],
          $post['points'],
          null,
          null,
          null,
          $user1 = New UserEntity($post['user_id']['user2'])
        );

        $expectedUserAdded3 = new UserSessionEntity(
          1,
          $expectedSession,
          $post['user_id']['user3'],
          $post['approved'],
          $post['points'],
          null,
          null,
          null,
          $user1 = New UserEntity($post['user_id']['user3'])
        );

        $expectedUsersAdded = [$expectedUserAdded1, $expectedUserAdded2, $expectedUserAdded3];

        $expectedUsersSession = [
          new UserSessionEntity(
            1,
            $expectedSession,
            4,
            1,
            0,
            null,
            null,
            null,
            $user1 = New UserEntity(2)),
          $expectedUserAdded1,
          $expectedUserAdded2,
          $expectedUserAdded3
        ];

                $data1 = [
                    'isApproved' => 1,
                    'points'     => 0,
                    'idSession'  => 2,
                    'idUser'     => 1
                ];

                $data2 = [
                    'isApproved' => 1,
                    'points'     => 0,
                    'idSession'  => 2,
                    'idUser'     => 2
                ];

                $data3 = [
                    'isApproved' => 1,
                    'points'     => 0,
                    'idSession'  => 2,
                    'idUser'     => 3
                ];

        $map = [
            [$data1, null, $expectedUserAdded1],
            [$data2, null, $expectedUserAdded2],
            [$data3, null, $expectedUserAdded3],
        ];

        $sessionService->method('fetch')->willReturn($expectedSession);
        $userSessionService->method('fetchAll')->willReturn($expectedUsersSession);
        //$userSessionService->method('add')->willReturn('holaaa');
        $userSessionService->method('add')->will($this->returnValueMap($map));

        $controller = $this->createController($view, $userSessionService, $userService, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedUsersSession as $userSession) {
          $expectedUsersSessionArray[] = $userSession->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']                 = $expectedSession->toArray();
            $expectedDatosUI['session']['usersSession'] = $expectedUsersSessionArray;
            $expectedDatosUI['breadcrumb']              = 'Usuarios de Sesión';
            $expectedDatosUI['message']                 = ['Se agregó exitosamente.', 'Se agregó exitosamente.', 'Se agregó exitosamente.'];
        }

        if ($view instanceof JsonView) {
            foreach ($expectedUsersAdded as $userAdded) {
                $expectedUsersAddedArray[] = $userAdded->toArray();
            }

            $expectedDatosUI  = $expectedUsersAddedArray;
            $expectedResponse = $response->withStatus(201);
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function testAdd() 
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession'     => 2
        ];

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

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('userSession/listAll.html.twig'),
        );

        $controller->add($request, $response, $args);
    }

    public function testAddWithJsonView() 
    {
        $view = $this->createMock(JsonView::class);

        $request = $this->createMock(Slim\Psr7\Request::class);

        $request->method('getParsedBody')->willReturn(
          [
            'userSession to add',
            'idSession' => 2,
            'user_id'   => [1],
            'start'     => '2019-06-27 19:00:00',
            'approved'  => 1,
            'points'    => 0,
            'idSession' => 2
          ]
        );

        $response         = new Slim\Psr7\Response();

        $args = [
          'idSession'     => 2
        ];

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

    public function addAndUpdateWithExceptionSetup($view, $exception)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getParsedBody')->willReturn(
          [
            'idSession' => 2,
            'user_id'   => [1],
            'start'     => '2019-06-27 19:00:00',
            'end'       => '2019-06-27 23:00:00',
            'approved'  => 1,
            'points'    => 0,
            'idSession' => 2
          ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $userSessionService = $this->createMock(UserSessionService::class);
        $userService        = $this->createMock(UserService::class);
        $sessionService     = $this->createMock(SessionService::class);

        $post = $request->getParsedBody();

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

        $sessionService->method('fetch')->willReturn($expectedSession);
        $userSessionService->method('fetchAll')->willReturn($expectedUsersSession);
        $userSessionService->method('add')->will($this->throwException($exception));
        $userSessionService->method('update')->will($this->throwException($exception));

        $controller = $this->createController($view, $userSessionService, $userService, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedUsersSession as $userSession) {
          $expectedUsersSessionArray[] = $userSession->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']                 = $expectedSession->toArray();
            $expectedDatosUI['session']['usersSession'] = $expectedUsersSessionArray;
            $expectedDatosUI['breadcrumb']              = 'Usuarios de Sesión';
            $expectedDatosUI['message']                 = [$exception->getMessage()];
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function testAddWithTableIsFull() 
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new TableIsFullException();

        $setup            = $this->addAndUpdateWithExceptionSetup($view, $exception);
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
            //$this->equalTo($expectedDatosUI),
            $this->contains([$exception->getMessage()]),
        );

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('userSession/listAll.html.twig'),
        );

        $controller->add($request, $response, $args);
    }

    public function testAddWithTableIsFullWithJsonView() 
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new TableIsFullException();

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

    public function testAddAlreadedAdded() 
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new UserSessionAlreadyAddedException();

        $setup            = $this->addAndUpdateWithExceptionSetup($view, $exception);
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
            //$this->equalTo($expectedDatosUI),
            $this->contains([$exception->getMessage()]),
        );

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('userSession/listAll.html.twig'),
        );

        $controller->add($request, $response, $args);
    }

    public function testAddAlreadedAddedWithJsonView() 
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new UserSessionAlreadyAddedException();

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

    public function testAddWithException() 
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\UserSessionEntity' . " Entity not found", 404); 

        $setup            = $this->addAndUpdateWithExceptionSetup($view, $exception);
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
            //$this->equalTo($expectedDatosUI),
            $this->contains([$exception->getMessage()]),
        );

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('userSession/listAll.html.twig'),
        );

        $controller->add($request, $response, $args);
    }

    public function testAddWithExceptionWithJsonView() 
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

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

    public function testAddMany() 
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession'     => 2
        ];

        $setup            = $this->addManySetup($view);
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

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('userSession/listAll.html.twig'),
        );

        $controller->add($request, $response, $args);
    }

    public function testAddManyWithJsonView() 
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession'     => 2
        ];

        $setup            = $this->addManySetup($view);
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

    public function testForm()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $userSessionService = $this->createMock(UserSessionService::class);
        $userService = $this->createMock(UserService::class);
        $sessionService = $this->createMock(SessionService::class);

        $args = [
          'idSession'  => 2
        ];

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

        $sessionService->method('fetch')->willReturn($expectedSession);
        $userService->method('fetchAll')->willReturn($expectedUsers);
        
        $controller = $this->createController($view, $userSessionService, $userService, $sessionService);

        $request    = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $expectedDatosUI = [];

        foreach ($expectedUsers as $user) {
          $expectedUsersArray[] = $user->toArray();
        }
        
        $expectedDatosUI['session']    = $expectedSession->toArray();
        $expectedDatosUI['users']      = $expectedUsersArray;
        $expectedDatosUI['breadcrumb'] = 'Nuevo UserSession';


        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($response),
            $this->equalTo($expectedDatosUI),
        );

        $controller->form($request, $response, $args);

    }

    public function testFormWithException()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $userSessionService = $this->createMock(UserSessionService::class);
        $userService        = $this->createMock(UserService::class);
        $sessionService     = $this->createMock(SessionService::class);

        $args = [
          'idSession'  => 2
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404);
        $expectedUsers = $this->getAListOfUsers();

        $sessionService->method('fetch')->will($this->throwException($exception));
        $userService->method('fetchAll')->willReturn($expectedUsers);
        
        $controller = $this->createController($view, $userSessionService, $userService, $sessionService);

        $request    = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;


        $expectedDatosUI = [];

        foreach ($expectedUsers as $user) {
          $expectedUsersArray[] = $user->toArray();
        }

        $expectedDatosUI['session']    = [];
        $expectedDatosUI['users']      = $expectedUsersArray;
        $expectedDatosUI['breadcrumb'] = 'Nuevo UserSession';
        $expectedDatosUI['message']    = [$exception->getMessage()];


        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($response),
            $this->equalTo($expectedDatosUI),
        );

        $controller->form($request, $response, $args);

    }

    public function updateSetup($view)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getParsedBody')->willReturn(
          [
            'idSession'         => 2,
            'user_id'           => [1],
            'start'             => '2019-06-27 19:00:00',
            'end'               => '2019-06-27 23:00:00',
            'approved'          => 1,
            'accumulatedPoints' => 0,
            'idSession'         => 2
          ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $userSessionService = $this->createMock(UserSessionService::class);
        $userService = $this->createMock(UserService::class);
        $sessionService = $this->createMock(SessionService::class);

        $post = $request->getParsedBody();

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

        $expectedUserSessionUpdated = new UserSessionEntity(
          1,
          $expectedSession,
          $post['user_id'][0],
          $post['approved'],
          $post['accumulatedPoints'],
          null,
          date_create($post['start']),
          null,
          $user1 = New UserEntity($post['user_id'][0])
        );

        $expectedUsersSession = [
          new UserSessionEntity(
            1,
            $expectedSession,
            2,
            1,
            0,
            null,
            date_create('2019-06-27 19:00:00'),
            null,
            $user1 = New UserEntity(2)),
          $expectedUserSessionUpdated
        ];

        $sessionService->method('fetch')->willReturn($expectedSession);
        $userSessionService->method('fetchAll')->willReturn($expectedUsersSession);
        $userSessionService->method('add')->willReturn($expectedUserSessionUpdated);

        $controller = $this->createController($view, $userSessionService, $userService, $sessionService);

        $expectedDatosUI = null;

        foreach ($expectedUsersSession as $userSession) {
          $expectedUsersSessionArray[] = $userSession->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']                 = $expectedSession->toArray();
            $expectedDatosUI['session']['usersSession'] = $expectedUsersSessionArray;
            $expectedDatosUI['breadcrumb']              = 'Usuarios de Sesión';
            $expectedDatosUI['message']                 = ['El usuario se actualizó exitosamente'];
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = $expectedUsersSessionAdded->toArray();
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function testUpdate()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession'     => 2,
          'idusersession' => 1
        ];

        // metthod with data post use 2 parameters in addSetup
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

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('userSession/listAll.html.twig'),
        );

        $controller->update($request, $response, $args);
    }


    public function testUpdateWithUserSessionNotFoundException() 
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new UserSessionNotFoundException();

        $setup            = $this->addAndUpdateWithExceptionSetup($view, $exception);
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
            //$this->equalTo($expectedDatosUI),
            $this->contains([$exception->getMessage()]),
        );

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('userSession/listAll.html.twig'),
        );

        $controller->update($request, $response, $args);
    }

    public function testUpdateWithUserSessionNotFoundExceptionWithJsonView() 
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new UserSessionNotFoundException();

        $setup            = $this->addAndUpdateWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];

        $expectedResponse = $response->withStatus(404);

        var_dump($expectedDatosUI);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->update($request, $response, $args);
    }

    public function testUpdateWithException() 
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\UserSessionEntity' . " Entity not found", 404); 

        $setup            = $this->addAndUpdateWithExceptionSetup($view, $exception);
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
            //$this->equalTo($expectedDatosUI),
            $this->contains([$exception->getMessage()]),
        );

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('userSession/listAll.html.twig'),
        );

        $controller->update($request, $response, $args);
    }

    public function testUpdateWithExceptionWithJsonView() 
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

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

        $controller->update($request, $response, $args);
    }

    public function deleteSetup($view)
    {
        $userSessionService = $this->createMock(UserSessionService::class);
        $userService        = $this->createMock(UserService::class);
        $sessionService     = $this->createMock(SessionService::class);

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

        $sessionService->method('fetch')->willReturn($expectedSession);
        $userSessionService->method('fetchAll')->willReturn($expectedUsersSession);
        $userSessionService->method('delete')->willReturn(true);

        $controller = $this->createController($view, $userSessionService, $userService, $sessionService);

        $request = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $args = [
          'idSession'     => 2,
          'idusersession' => 1
        ];

        $expectedDatosUI = null;

        foreach ($expectedUsersSession as $userSession) {
          $expectedUsersSessionArray[] = $userSession->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']                 = $expectedSession->toArray();
            $expectedDatosUI['session']['usersSession'] = $expectedUsersSessionArray;
            $expectedDatosUI['breadcrumb']              = 'Usuarios de Sesión';
            $expectedDatosUI['message']                 = ['El usuario se eliminó exitosamente de la sesión'];
        }

        if ($view instanceof JsonView) {
            $expectedResponse = $response->withStatus(204);
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
        $userSessionService = $this->createMock(UserSessionService::class);
        $userService        = $this->createMock(UserService::class);
        $sessionService     = $this->createMock(SessionService::class);

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

        $sessionService->method('fetch')->willReturn($expectedSession);
        $userSessionService->method('fetchAll')->willReturn($expectedUsersSession);
        $userSessionService->method('delete')->will($this->throwException($exception));

        $controller = $this->createController($view, $userSessionService, $userService, $sessionService);

        $request = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $args = [
          'idSession'     => 2,
          'idusersession' => 1
        ];

        $expectedDatosUI = null;

        foreach ($expectedUsersSession as $userSession) {
          $expectedUsersSessionArray[] = $userSession->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']                 = $expectedSession->toArray();
            $expectedDatosUI['session']['usersSession'] = $expectedUsersSessionArray;
            $expectedDatosUI['breadcrumb']              = 'Usuarios de Sesión';
            $expectedDatosUI['message']                 = [$exception->getMessage()];
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function testDelete()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession'   => 2,
          'idusersession' => 1
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

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('userSession/listAll.html.twig'),
        );

        $controller->delete($request, $response, $args);
    }

    public function testDeleteWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession'   => 2,
          'idusersession' => 1
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

    public function testDeleteWithUserSessionNotFoundException()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession'   => 2,
          'idusersession' => 1
        ];

        $exception = new UserSessionNotFoundException(); 

        $setup            = $this->deleteWithExceptionSetup($view, $exception);
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
            $this->contains([$exception->getMessage()]),
        );

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('userSession/listAll.html.twig'),
        );

        $controller->delete($request, $response, $args);
    }


    public function testDeleteWithUserSessionNotFoundWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession'   => 2,
          'idusersession' => 1
        ];

        $exception = new UserSessionNotFoundException(); 

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

    public function testDeleteWithException()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession'   => 2,
          'idusersession' => 1
        ];

        $exception = new \Exception('Solcre\Pokerclub\Entity\UserSessionEntity' . " Entity not found", 404); 

        $setup            = $this->deleteWithExceptionSetup($view, $exception);
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
            $this->contains([$exception->getMessage()]),
        );

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('userSession/listAll.html.twig'),
        );

        $controller->delete($request, $response, $args);
    }


    public function testDeleteWithExceptionWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession'   => 2,
          'idusersession' => 1
        ];

        $exception = new \Exception('Solcre\Pokerclub\Entity\UserSessionEntity' . " Entity not found", 404); 

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

    public function testFormClose()
    {

        $view = $this->createMock(TwigWrapperView::class);

        $userSessionService = $this->createMock(UserSessionService::class);
        $userService        = $this->createMock(UserService::class);
        $sessionService     = $this->createMock(SessionService::class);

        $args = [
          'idSession'  => 2,
          'idusersession' => 1
        ];

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

        $sessionService->method('fetch')->willReturn($expectedSession);
        $userSessionService->method('fetch')->willReturn($expectedUserSession);

        $controller = $this->createController($view, $userSessionService, $userService, $sessionService);

        $request = $this->createMock(Slim\Psr7\Request::class);
        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;


        $expectedDatosUI = [];

        // $expectedDatosUI['session']  = $expectedSession->toArray();
        $expectedDatosUI['userSession'] = $expectedUserSession->toArray();
        $expectedDatosUI['breadcrumb']  = 'Cerrar Session de Usuario';


        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->formClose($request, $response, $args);
    }

    public function testUserSessionNotFoundFormClose()
    {

        $view = $this->createMock(TwigWrapperView::class);

        $userSessionService = $this->createMock(UserSessionService::class);
        $userService        = $this->createMock(UserService::class);
        $sessionService     = $this->createMock(SessionService::class);

        $args = [
          'idSession'  => 2,
          'idusersession' => 1
        ];

        $exception = new UserSessionNotFoundException();

        $userSessionService->method('fetch')->will($this->throwException($exception));

        $controller = $this->createController($view, $userSessionService, $userService, $sessionService);

        $request          = $this->createMock(Slim\Psr7\Request::class);
        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;


        $expectedDatosUI = [];

        // $expectedDatosUI['session']               = $expectedSession->toArray();
        $expectedDatosUI['userSession'] = [];
        $expectedDatosUI['breadcrumb']  = 'Cerrar Session de Usuario';
        $expectedDatosUI['message']     = [$exception->getMessage()];


        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->formClose($request, $response, $args);
    }

    public function testFormCloseWithException()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $userSessionService = $this->createMock(UserSessionService::class);
        $userService        = $this->createMock(UserService::class);
        $sessionService     = $this->createMock(SessionService::class);

        $args = [
          'idSession'  => 2,
          'idusersession' => 1
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\UserSessionEntity' . " Entity not found", 404);

        $userSessionService->method('fetch')->will($this->throwException($exception));
        $controller = $this->createController($view, $userSessionService, $userService, $sessionService);

        $request = $this->createMock(Slim\Psr7\Request::class);
        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;


        $expectedDatosUI = [];

        // $expectedDatosUI['session']               = $expectedSession->toArray();
        $expectedDatosUI['userSession'] = [];
        $expectedDatosUI['breadcrumb']  = 'Cerrar Session de Usuario';
        $expectedDatosUI['message']     = [$exception->getMessage()];


        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->formClose($request, $response, $args);
      }

    public function closeSetup($view)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);

        $request->method('getParsedBody')->willReturn(
          [
            'id'        => 2,
            'idSession' => 1,
            'idUser'    => '1',
            'end'       => '2019-06-27 23:00:00',
            'cashout'   => 150,
          ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $userSessionService = $this->createMock(UserSessionService::class);
        $userService        = $this->createMock(UserService::class);
        $sessionService     = $this->createMock(SessionService::class);

        $post = $request->getParsedBody();

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

        $expectedUserSessionClose = new UserSessionEntity(
          1,
          $expectedSession,
          $post['idUser'],
          null,
          null,
          null,
          null, // date_create($post['start']),
          date_create($post['end']),
          $user1 = New UserEntity($post['idUser'])
        );

        $expectedUsersSession = $this->getAListOfUsersSession($expectedSession);

        $sessionService->method('fetch')->willReturn($expectedSession);
        $userSessionService->method('fetchAll')->willReturn($expectedUsersSession);

        $userSessionService->method('close')->willReturn($expectedUserSessionClose);

        $controller = $this->createController($view, $userSessionService, $userService, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedUsersSession as $userSession) {
          $expectedUsersSessionArray[] = $userSession->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']                 = $expectedSession->toArray();
            $expectedDatosUI['session']['usersSession'] = $expectedUsersSessionArray;
            $expectedDatosUI['breadcrumb']              = 'Usuarios de Sesión';
            $expectedDatosUI['message']                 = ['El usuario ha salido de la sesión'];
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = $expectedUserSessionClose->toArray();
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function closeSetupWithException($view, $exception)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);

        $request->method('getParsedBody')->willReturn(
          [
            'id'        => 2,
            'idSession' => 1,
            'idUser'    => '1',
            'end'       => '2019-06-27 23:00:00',
            'cashout'   => 150,
          ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $userSessionService = $this->createMock(UserSessionService::class);
        $userService        = $this->createMock(UserService::class);
        $sessionService     = $this->createMock(SessionService::class);

        $post = $request->getParsedBody();

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

        $sessionService->method('fetch')->willReturn($expectedSession);
        $userSessionService->method('fetchAll')->willReturn($expectedUsersSession);

        $userSessionService->method('close')->will($this->throwException($exception));

        $controller = $this->createController($view, $userSessionService, $userService, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedUsersSession as $userSession) {
          $expectedUsersSessionArray[] = $userSession->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']                 = $expectedSession->toArray();
            $expectedDatosUI['session']['usersSession'] = $expectedUsersSessionArray;
            $expectedDatosUI['breadcrumb']              = 'Usuarios de Sesión';
            $expectedDatosUI['message']                 = [$exception->getMessage()];
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function testClose()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession'     => 2,
          'idusersession' => 1
        ];

        $setup            = $this->closeSetup($view);
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

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('userSession/listAll.html.twig'),
        );

        $controller->close($request, $response, $args);
    }

    public function testCloseWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession'     => 2,
          'idusersession' => 1
        ];

        $setup            = $this->closeSetup($view);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $response->withStatus(200);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->close($request, $response, $args);
    }

    public function testCloseWithExeption()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession'     => 2,
          'idusersession' => 1
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404);

        $setup            = $this->closeSetupWithException($view, $exception);
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

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('userSession/listAll.html.twig'),
        );

        $controller->close($request, $response, $args);
    }

    public function testCloseWithExeptionWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession'     => 2,
          'idusersession' => 1
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404);

        $setup            = $this->closeSetupWithException($view, $exception);
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

        $controller->close($request, $response, $args);
    }
}
