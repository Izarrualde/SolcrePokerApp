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
use Test\AppWrapper;
use Solcre\lmsuy\Controller\BuyinSessionController;
use Solcre\lmsuy\View\TwigWrapperView;
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

        $sessionService->method('fetchOne')->willReturn($expectedSession);
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

    public function testListAllWithJsonView()
    {
        // check that invoke controller listAll with parameters $request, $response and $datosUI
        // response contains the right statusCode 

        $request  = $this->createMock(Slim\Psr7\Request::class);
        $view     = $this->createMock(Solcre\lmsuy\View\JsonView::class);

        $response = new Slim\Psr7\Response();

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
            $this->equalTo($response),
            $this->equalTo($expectedDatosUI),
        );

        $controller->listAll($request, $response, $args);
    }


    public function testListAll()
    {
        $request  = $this->createMock(Slim\Psr7\Request::class);
        $view     = $this->createMock(Solcre\lmsuy\View\TwigWrapperView::class);

        $response = new Slim\Psr7\Response();

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
            $this->equalTo($response),
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

        $sessionService->method('fetchOne')->willReturn($expectedSession);
        $buyinSessionService->method('fetchOne')->willReturn($expectedBuyin);

        $controller = $this->createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService);

        $expectedDatosUI = [];


        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']           = $expectedSession->toArray();
            $expectedDatosUI['session']['buyin'] = $expectedBuyin->toArray();
            $expectedDatosUI['breadcrumb']        = 'Editar Buyin';
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = $expectedBuyin->toArray();
        }

        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI 
        ];

    } 

    public function testList()
    {
        // check that invoke controller listAll with parameters $request, $response and $datosUI

        $view = $this->createMock(Solcre\lmsuy\View\TwigWrapperView::class);
        $request = $this->createMock(Slim\Psr7\Request::class);

        $response = new Slim\Psr7\Response();

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
            $this->equalTo($response),
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

        $response = new Slim\Psr7\Response();

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
            $this->equalTo($response),
            $this->equalTo($expectedDatosUI),
        );

        $controller->list($request, $response, $args);
    }

    public function addSetup($view, $request)
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
            $request->getParsedBody()['amountCash'],
            $request->getParsedBody()['amountCredit'],
            $expectedSession->getSessionUsers()[1],
            $request->getParsedBody()['hour'],
            1,
            $request->getParsedBody()['approved'],
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
          new BuyinSessionEntity(
              $expectedBuyinAdded->getId(),
              $expectedBuyinAdded->getAmountCash(),
              $expectedBuyinAdded->getAmountCredit(),
              $expectedBuyinAdded->getUserSession(),
              $expectedBuyinAdded->getHour(),
              $expectedBuyinAdded->getCurrency(),
              $expectedBuyinAdded->getIsApproved()
          )
        ];

        $sessionService->method('fetchOne')->willReturn($expectedSession);
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
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI 
        ];
    }

    public function testAdd()
    {
        // check that invoke controller listAll with parameters $request, $response and $datosUI
        // response contains the right statusCode 
        // check that template is right

        $view = $this->createMock(TwigWrapperView::class);
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

        $response = new Slim\Psr7\Response();

        $args = [
          'idSession' => 2
        ];

        // metthod with data post use 2 parameters in addSetup
        $setup           = $this->addSetup($view, $request);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($response),
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

        // $response = $this->createMock(Slim\Psr7\Response::class);
        $response = new Slim\Psr7\Response();
        

        $args = [
          'idSession' => 2
        ];

        $setup           = $this->addSetup($view, $request);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];
        
        // set right response
        $response = $response->withStatus(201);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($response),
            $this->equalTo($expectedDatosUI),
        );

        $controller->add($request, $response, $args);
    }

    public function addInvalidSetup($view)
    {
        $buyinSessionService = $this->createMock(BuyinSessionService::class);
        $userSessionService  = $this->createMock(UserSessionService::class);
        $userService         = $this->createMock(UserService::class);
        $sessionService      = $this->createMock(SessionService::class);

        $sessionService->method('fetchOne')->willReturn(null);
        $buyinSessionService->method('fetchAllBuyins')->willReturn(null);
        $exception = new BuyinInvalidException();
        $buyinSessionService->method('add')->will($this->throwException($exception));


        $controller = $this->createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService); 

        $expectedDatosUI = [];

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI[] = $exception->getMessage();
        }

        /*if ($view instanceof JsonView) {
            $expectedDatosUI = $expectedBuyinAdded->toArray();
            // $response = with status correspondiente
        }*/

        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI 
        ];

    }

    public function testAddWithInvalidBuyin()
    {
        $view = $this->createMock(TwigWrapperView::class);

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

        $response = new Slim\Psr7\Response();

        $args = [
          'idSession' => 2
        ];

        $setup           = $this->addInvalidSetup($view);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];
        
        $exception = new BuyinInvalidException();

        $response = $response->withStatus(400);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($response),
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

        $response = new Slim\Psr7\Response();

        $args = [
          'idSession' => 2
        ];

        $setup           = $this->addInvalidSetup($view);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];

        $response = $response->withStatus(400);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($response),
            $this->equalTo($expectedDatosUI),
        );

        $controller->add($request, $response, $args);
    }

    public function testForm()
    {
        $view = $this->createMock(TwigWrapperView::class);
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
        $request    = $this->createMock(Slim\Psr7\Request::class);
        $response = new Slim\Psr7\Response();
        $args = [
          'idSession'  => 2
        ];

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
                'id'           =>2,
                'amountCash'   => 200,
                'amountCredit' => 300,
                'hour'         => '2019-06-26 19:25:00',
                'idSession'    => 2,
                'approved'     => 1,
                'currency'     => 1
          ]
        );

        $response = new Slim\Psr7\Response();

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
            $request->getParsedBody()['id'],
            $request->getParsedBody()['amountCash'],
            $request->getParsedBody()['amountCredit'],
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
            $request->getParsedBody()['hour'],
            $request->getParsedBody()['currency'],
            $request->getParsedBody()['approved'],
        );


        $sessionService->method('fetchOne')->willReturn($expectedSession);
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
            // $response = with status correspondiente
        }

        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI,
            'request'         => $request,
            'response'        => $response
        ];
        
    }



    public function testUpdate()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $response = new Slim\Psr7\Response();

        $args = [
          'idSession' => 2
        ];

        $setup           = $this->updateSetup($view);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];
        $request         = $setup['request'];
        $response        = $setup['response'];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($response),
            $this->equalTo($expectedDatosUI),
        );

        $controller->update($request, $response, $args);
    }

    public function testUpdateWithJson()
    {
        $view = $this->createMock(JsonView::class);

        $response = new Slim\Psr7\Response();

        $args = [
          'idSession' => 2
        ];

        $setup           = $this->updateSetup($view);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];
        $request         = $setup['request'];
        $response        = $setup['response'];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($response),
            $this->equalTo($expectedDatosUI),
        );

        $controller->update($request, $response, $args);

    }


    public function deleteSetup($view)
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

        $sessionService->method('fetchOne')->willReturn($expectedSession);
        $buyinSessionService->method('fetchAllBuyins')->willReturn($expectedBuyins);
        $buyinSessionService->method('delete')->willReturn(true);

        $controller = $this->createController($view, $buyinSessionService, $userSessionService, $userService, $sessionService);
        
        $request    = $this->createMock(Slim\Psr7\Request::class);
 

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


        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI,
            'request'         => $request,
        ];
    }

    public function testDelete()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2,
          'idbuyin'   => 1
        ];

        $response = new Slim\Psr7\Response();

        $setup           = $this->deleteSetup($view);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];
        $request         = $setup['request'];



        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($response),
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

        $response = new Slim\Psr7\Response();

        $setup           = $this->deleteSetup($view);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];
        $request         = $setup['request'];

        // set right response
        $response = $response->withStatus(204);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($response),
            $this->equalTo($expectedDatosUI),
        );

        $controller->delete($request, $response, $args);
    }


}