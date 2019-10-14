<?php

use PHPUnit\Framework\TestCase;
use \Solcre\Pokerclub\Service\BuyinSessionService;
use \Solcre\Pokerclub\Service\SessionService;
use \Solcre\Pokerclub\Service\UserSessionService;
use \Solcre\Pokerclub\Service\UserService;
use \Solcre\Pokerclub\Entity\BuyinSessionEntity;
use \Solcre\Pokerclub\Entity\SessionEntity;
use \Solcre\Pokerclub\Entity\UserSessionEntity;
use \Solcre\Pokerclub\Entity\UserEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;
use Solcre\Pokerclub\Exception\BuyinInvalidException;
use Solcre\Pokerclub\Exception\BuyinNotFoundException;
use Test\AppWrapper;
use Solcre\lmsuy\Controller\BuyinSessionController;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;
use Doctrine\Common\Collections\ArrayCollection;

class BuyinSessionControllerTest extends TestCase
{
    public function createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService) 
    {
        $container = AppWrapper::getContainer();

        // Get EntityManager from container
        $entityManager = $container->get(EntityManager::class);
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
        $user1 = New UserEntity(1);
        $user2 = New UserEntity(2);

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

    public function listAllSetup($view)
    {
        $buyinSessionService = $this->createMock(BuyinSessionService::class);
        $userSessionService  = $this->createMock(UserSessionService::class);
        $userService         = $this->createMock(UserService::class);
        $sessionService      = $this->createMock(SessionService::class);

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

        $sessionService->method('fetch')->willReturn($expectedSession);
        $buyinSessionService->method('fetchAllBuyins')->willReturn($expectedBuyins);

        $controller = $this->createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedBuyins as $buyin) {
            $buyins[] = $buyin->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']           = $expectedSession->toArray();
            $expectedDatosUI['session']['buyins'] = $buyins;
            $expectedDatosUI['breadcrumb']        = 'Buyins';
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = $buyins;
        }

        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI 
        ];
    }

    public function listAllWithExceptionSetup($view, $exception)
    {
        $buyinSessionService = $this->createMock(BuyinSessionService::class);
        $userSessionService  = $this->createMock(UserSessionService::class);
        $userService         = $this->createMock(UserService::class);
        $sessionService      = $this->createMock(SessionService::class);

        $sessionService->method('fetch')->will($this->throwException($exception));
        $buyinSessionService->method('fetchAllBuyins')->willReturn(null);

        $controller = $this->createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService);

        $expectedDatosUI = [];

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['message']    = [$exception->getMessage()];
            $expectedDatosUI['breadcrumb'] = 'Buyins';
        }

        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI 
        ];
    }

    public function testListAll()
    {
        $view             = $this->createMock(TwigWrapperView::class);
        $request          = $this->createMock(Slim\Psr7\Request::class);
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
        // check that invoke controller listAll with parameters $request, $response and $datosUI
        // response contains the right statusCode 
        $view     = $this->createMock(Solcre\lmsuy\View\JsonView::class);
        $request  = $this->createMock(Slim\Psr7\Request::class);

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

    public function testListAllWithException()
    {
        $view             = $this->createMock(TwigWrapperView::class);
        $request          = $this->createMock(Slim\Psr7\Request::class);
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
            $this->equalTo($expectedDatosUI),
        );

        $controller->listAll($request, $response, $args);
    }

    public function testListAllWithExceptionWithJsonView()
    {
        $view             = $this->createMock(JsonView::class);
        $request          = $this->createMock(Slim\Psr7\Request::class);
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

        $sessionService->method('fetch')->willReturn($expectedSession);
        $buyinSessionService->method('fetch')->willReturn($expectedBuyin);

        $controller = $this->createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService);

        $expectedDatosUI = [];


        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']          = $expectedSession->toArray();
            $expectedDatosUI['session']['buyin'] = $expectedBuyin->toArray();
            $expectedDatosUI['breadcrumb']       = 'Editar Buyin';
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = $expectedBuyin->toArray();
        }

        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI 
        ];

    } 

    public function listWithExceptionSetup($view, $exception)
    {
        $buyinSessionService = $this->createMock(BuyinSessionService::class);
        $userSessionService = $this->createMock(UserSessionService::class);
        $userService = $this->createMock(UserService::class);
        $sessionService = $this->createMock(SessionService::class);

        $sessionService->method('fetch')->willReturn(null);
        $buyinSessionService->method('fetch')->will($this->throwException($exception));

        $controller = $this->createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService);

        $expectedDatosUI = [];

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['message']    = [$exception->getMessage()];
            $expectedDatosUI['breadcrumb'] = 'Editar Buyin';
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
            'idSession' => 2,
            'idbuyin'   => 1
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
        // check that invoke controller listAll with parameters $request, $response and $datosUI
        // response contains the right statusCode 

        $view = $this->createMock(Solcre\lmsuy\View\JsonView::class);
        $request = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response->withStatus(200);

        $args = [
            'idSession' => 2,
            'idbuyin'   => 1
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

    public function testListBuyinNotFoundException()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $request          = $this->createMock(Slim\Psr7\Request::class);
        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $args = [
            'idSession' => 2,
            'idbuyin'   => 1
        ];

        $exception = New BuyinNotFoundException();
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

    public function testListBuyinNotFoundExceptionWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $request          = $this->createMock(Slim\Psr7\Request::class);
        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response->withStatus(404);

        $args = [
            'idSession' => 2,
            'idbuyin'   => 1
        ];

        $exception = New BuyinNotFoundException();
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

    public function testListWithException()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $request          = $this->createMock(Slim\Psr7\Request::class);
        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $args = [
            'idSession' => 2,
            'idbuyin'   => 1
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\ComissionSessionEntity' . " Entity not found", 404);
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
        $expectedResponse = $response->withStatus(500);

        $args = [
            'idSession' => 2,
            'idbuyin'   => 1
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\ComissionSessionEntity' . " Entity not found", 404);
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
        $request    = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getParsedBody')->willReturn(
          [
            'amountCash'    => 200,
            'amountCredit'  => 300,
            'approved'      => 1,
            'idUserSession' => 2,
            'idSession'     => 2,
            'hour'          => '2019-06-26 19:05:00'
          ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $buyinSessionService = $this->createMock(BuyinSessionService::class);
        $userSessionService  = $this->createMock(UserSessionService::class);
        $userService         = $this->createMock(UserService::class);
        $sessionService      = $this->createMock(SessionService::class);
        
        $post = $request->getParsedBody();

        $expectedSession = new SessionEntity(
            2,
            date_create('2019-06-27 15:00:00'),
            'another test session',
            'another test description',
            'another test photo',
            9,
            date_create('2019-06-27 19:00:00'),
            date_create('2019-06-27 19:30:00'),
            date_create('2019-06-27 23:30:00')
        );

        $user1 = New UserEntity(1);
        $user2 = New UserEntity(2);

        $userSession1 = new UserSessionEntity(
            1,
            $expectedSession,
            1,
            1,
            0,
            0,
            null,
            null,
            $user1
        );

        $userSession2 = new UserSessionEntity(
            $request->getParsedBody()['idUserSession'],
            $expectedSession,
            1,
            1,
            0,
            0,
            null,
            null,
            $user2
        );

        $userSessions = new ArrayCollection();
        $userSessions[] = $userSession1;
        $userSessions[] = $userSession2;
        $expectedSession->setSessionUsers($userSessions);

        $expectedBuyinAdded = new BuyinSessionEntity(
            2,
            $post['amountCash'],
            $post['amountCredit'],
            $expectedSession->getSessionUsers()[1],
            date_create($post['hour']),
            1,
            $post['approved'],
        );

        $expectedBuyins = [ 
          new BuyinSessionEntity(
              1,
              100,
              200,
              $userSession1,
              date_create('2019-06-26 19:00:00'),
              1,
              1
          ),
          $expectedBuyinAdded
        ];

        $sessionService->method('fetch')->willReturn($expectedSession);
        $buyinSessionService->method('add')->willReturn($expectedBuyinAdded);
        $buyinSessionService->method('fetchAllBuyins')->willReturn($expectedBuyins);

        $controller = $this->createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedBuyins as $buyin) {
            $expectedBuyinsArray[] = $buyin->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']           = $expectedSession->toArray();
            $expectedDatosUI['session']['buyins'] = $expectedBuyinsArray;
            $expectedDatosUI['breadcrumb']        = 'Buyins';
            $expectedDatosUI['message']           = ['El buyin se agregó exitosamente'];
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = $expectedBuyinAdded->toArray();
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function addAndUpdateWithExceptionSetup($view, $exception)
    {
        $request    = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getParsedBody')->willReturn(
          [
            'amountCash'    => 'a non numeric value',
            'amountCredit'  => 300,
            'approved'      => 1,
            'idUserSession' => 2,
            'idSession'     => 2,
            'hour'          => '2019-06-26 19:05:00'
          ]
        );

        $response = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $buyinSessionService = $this->createMock(BuyinSessionService::class);
        $userSessionService  = $this->createMock(UserSessionService::class);
        $userService         = $this->createMock(UserService::class);
        $sessionService      = $this->createMock(SessionService::class);

        $expectedSession = new SessionEntity(1);

        $expectedBuyins = $this->getAListOfBuyins($expectedSession);

        $sessionService->method('fetch')->willReturn($expectedSession);
        $buyinSessionService->method('fetchAllBuyins')->willReturn($expectedBuyins);
        $buyinSessionService->method('add')->will($this->throwException($exception));
        $buyinSessionService->method('update')->will($this->throwException($exception));

        $controller = $this->createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService); 

        $expectedDatosUI = [];

        foreach ($expectedBuyins as $buyin) {
            $expectedBuyinsArray[] = $buyin->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']           = $expectedSession->toArray();
            $expectedDatosUI['session']['buyins'] = $expectedBuyinsArray;
            $expectedDatosUI['breadcrumb']        = 'Buyins';
            $expectedDatosUI['message']           = [$exception->getMessage()];
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
        // check that invoke controller listAll with parameters $request, $response and $datosUI
        // response contains the right statusCode 
        // check that template is right

        $view = $this->createMock(TwigWrapperView::class);


        $args = [
          'idSession' => 2
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
            $this->equalTo('buyinSession/listAll.html.twig'),
        );

        $controller->add($request, $response, $args);
    }

    public function testAddWithJsonView()
    {
        $view = $this->createMock(JsonView::class);
        
        $args = [
          'idSession' => 2
        ];

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

    public function testAddWithInvalidBuyin()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new BuyinInvalidException();

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
            $this->contains([$exception->getMessage()]),
        );

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('buyinSession/listAll.html.twig'),
        );

        $controller->add($request, $response, $args);
    }

    public function testAddWithInvalidBuyinWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new BuyinInvalidException();

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

        $exception = new Exception('Solcre\Pokerclub\Entity\BuyinSessionEntity' . " Entity not found", 404);

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
            $this->contains([$exception->getMessage()]),
        );

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('buyinSession/listAll.html.twig'),
        );

        $controller->add($request, $response, $args);
    }

    public function testAddWithExceptionWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\BuyinSessionEntity' . " Entity not found", 404);

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

    public function testForm()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $request    = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $buyinSessionService = $this->createMock(BuyinSessionService::class);
        $userSessionService = $this->createMock(UserSessionService::class);
        $userService = $this->createMock(UserService::class);
        $sessionService = $this->createMock(SessionService::class);
        
        $args = [
          'idSession'  => 2
        ];

        $expectedSession = new SessionEntity(
            $args['idSession'],
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

        $controller = $this->createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedUsersSession as $userSession) {
          $expectedUsersSessionArray[] = $userSession->toArray();
        }

        $expectedDatosUI['session']                 = $expectedSession->toArray();
        $expectedDatosUI['session']['usersSession'] = $expectedUsersSessionArray;
        $expectedDatosUI['breadcrumb']              = 'Nuevo Buyin';

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->form($request, $response, $args);
    }

    public function testFormWithException()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $request          = $this->createMock(Slim\Psr7\Request::class);
        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $buyinSessionService = $this->createMock(BuyinSessionService::class);
        $userSessionService = $this->createMock(UserSessionService::class);
        $userService = $this->createMock(UserService::class);
        $sessionService = $this->createMock(SessionService::class);
        
        $args = [
          'idSession'  => 2
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404);        
        $sessionService->method('fetch')->will($this->throwException($exception));
        $userSessionService->method('fetchAll')->willReturn(null);

        $controller = $this->createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService);

        $expectedDatosUI = [];

        $expectedDatosUI['session']                 = [];
        $expectedDatosUI['session']['usersSession'] = [];
        $expectedDatosUI['message']                 = [$exception->getMessage()];
        $expectedDatosUI['breadcrumb']              = 'Nuevo Buyin';

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->form($request, $response, $args);
    }

    public function updateSetup($view)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getParsedBody')->willReturn(
            [
                'id'           => 2,
                'amountCash'   => 200,
                'amountCredit' => 300,
                'hour'         => '2019-06-26 19:25:00',
                'idSession'    => 2,
                'approved'     => 1,
                'currency'     => 1
          ]
        );

        $post = $request->getParsedBody();

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $buyinSessionService = $this->createMock(BuyinSessionService::class);
        $userSessionService  = $this->createMock(UserSessionService::class);
        $userService         = $this->createMock(UserService::class);
        $sessionService      = $this->createMock(SessionService::class);

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

        $expectedBuyinUpdated = new BuyinSessionEntity(
            $post['id'],
            $post['amountCash'],
            $post['amountCredit'],
            new UserSessionEntity(
                2,
                $expectedSession,
                1,
                1,
                0,
                0,
                null,
                null,
                new UserEntity(2)
            ),
            $post['hour'],
            date_create($post['currency']),
            $post['approved'],
        );

        $sessionService->method('fetch')->willReturn($expectedSession);
        $buyinSessionService->method('fetchAllBuyins')->willReturn($expectedBuyins);
        $buyinSessionService->method('update')->willReturn($expectedBuyinUpdated);

        $controller = $this->createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedBuyins as $buyin) {
          $buyins[] = $buyin->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']           = $expectedSession->toArray();
            $expectedDatosUI['session']['buyins'] = $buyins;
            $expectedDatosUI['breadcrumb']        = 'Buyins';
            $expectedDatosUI['message']           = ['El buyin se actualizó exitosamente'];
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = $expectedBuyinUpdated->toArray();
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

    public function testUpdate()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $setup           = $this->updateSetup($view);
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
            $this->equalTo('buyinSession/listAll.html.twig'),
        );

        $controller->update($request, $response, $args);
    }

    public function testUpdateWithJson()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
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

    public function testUpdateWithInvalidBuyin()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new BuyinInvalidException();

        $setup           = $this->addAndUpdateWithExceptionSetup($view, $exception);
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
            $this->equalTo('buyinSession/listAll.html.twig'),
        );

        $controller->update($request, $response, $args);
    }

    public function testUpdateWithInvalidBuyinWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new BuyinInvalidException();

        $setup           = $this->addAndUpdateWithExceptionSetup($view, $exception);
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

    public function testUpdateBuyinNotFound()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new BuyinNotFoundException();

        $setup           = $this->addAndUpdateWithExceptionSetup($view, $exception);
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
            $this->equalTo('buyinSession/listAll.html.twig'),
        );

        $controller->update($request, $response, $args);
    }

    public function testUpdateBuyinNotFoundWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new BuyinNotFoundException();

        $setup           = $this->addAndUpdateWithExceptionSetup($view, $exception);
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

    public function testUpdateWithException()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\BuyinSessionEntity' . " Entity not found", 404);

        $setup           = $this->addAndUpdateWithExceptionSetup($view, $exception);
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
            $this->equalTo('buyinSession/listAll.html.twig'),
        );

        $controller->update($request, $response, $args);
    }

    public function testUpdateWithExceptionWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\BuyinSessionEntity' . " Entity not found", 404);

        $setup           = $this->addAndUpdateWithExceptionSetup($view, $exception);
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
        $request    = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $buyinSessionService = $this->createMock(BuyinSessionService::class);
        $userSessionService  = $this->createMock(UserSessionService::class);
        $userService         = $this->createMock(UserService::class);
        $sessionService      = $this->createMock(SessionService::class);

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

        $sessionService->method('fetch')->willReturn($expectedSession);
        $buyinSessionService->method('fetchAllBuyins')->willReturn($expectedBuyins);
        $buyinSessionService->method('delete')->willReturn(true);

        $controller = $this->createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService);

        $expectedDatosUI = null;

        foreach ($expectedBuyins as $buyin) {
          $buyins[] = $buyin->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']           = $expectedSession->toArray();
            $expectedDatosUI['session']['buyins'] = $buyins;
            $expectedDatosUI['breadcrumb']        = 'Buyins';
            $expectedDatosUI['message']           = ['El buyin se eliminó exitosamente'];
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
        $request    = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $buyinSessionService = $this->createMock(BuyinSessionService::class);
        $userSessionService  = $this->createMock(UserSessionService::class);
        $userService         = $this->createMock(UserService::class);
        $sessionService      = $this->createMock(SessionService::class);

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

        $sessionService->method('fetch')->willReturn($expectedSession);
        $buyinSessionService->method('fetchAllBuyins')->willReturn($expectedBuyins);
        $buyinSessionService->method('delete')->will($this->throwException($exception));

        $controller = $this->createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService);

        $expectedDatosUI = null;

        foreach ($expectedBuyins as $buyin) {
          $buyins[] = $buyin->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']           = $expectedSession->toArray();
            $expectedDatosUI['session']['buyins'] = $buyins;
            $expectedDatosUI['breadcrumb']        = 'Buyins';
            $expectedDatosUI['message']           = [$exception->getMessage()];
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' =>  $expectedResponse
        ];
    }

    public function testDelete()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2,
          'idbuyin'   => 1
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
            $this->equalTo('buyinSession/listAll.html.twig'),
        );

        $controller->delete($request, $response, $args);
    }

    public function testDeleteWithJson()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2,
          'idbuyin'   => 1
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

    public function testDeleteBuyinNotFound()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2,
          'idbuyin'   => 1
        ];

        $exception = new BuyinNotFoundException();

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
            $this->equalTo($expectedDatosUI),
        );

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('buyinSession/listAll.html.twig'),
        );

        $controller->delete($request, $response, $args);
    }

    public function testDeleteBuyinNotFoundWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2,
          'idbuyin'   => 1
        ];

        $exception = new BuyinNotFoundException();

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
          'idSession' => 2,
          'idbuyin'   => 1
        ];

        $exception = new \Exception('Solcre\Pokerclub\Entity\BuyinSessionEntity' . " Entity not found", 404); 

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
            $this->equalTo($expectedDatosUI),
        );

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('buyinSession/listAll.html.twig'),
        );

        $controller->delete($request, $response, $args);
    }

    public function testDeleteWithExceptionWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2,
          'idbuyin'   => 1
        ];

        $exception = new \Exception('Solcre\Pokerclub\Entity\ComissionSessionEntity' . " Entity not found", 404); ;

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
