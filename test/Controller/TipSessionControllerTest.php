<?php

use PHPUnit\Framework\TestCase;
use \Solcre\lmsuy\Service\DealerTipSessionService;
use \Solcre\lmsuy\Service\ServiceTipSessionService;
use \Solcre\lmsuy\Service\SessionService;
use \Solcre\lmsuy\Entity\DealerTipSessionEntity;
use \Solcre\lmsuy\Entity\ServiceTipSessionEntity;
use \Solcre\lmsuy\Entity\SessionEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;
use \Solcre\lmsuy\Exception\DealerTipInvalidException;
use \Solcre\lmsuy\Exception\ServiceTipInvalidException;
use Test\AppWrapper;
use Solcre\lmsuy\Controller\TipSessionController;

class TipSessionControllerTest extends TestCase
{

  public function createController($view, $dealerTipService, $serviceTipService, $sessionService) {
      $container = AppWrapper::getContainer();

      // Get EntityManager from container
      $entityManager = $container->get(EntityManager::class);

      $viewMock = 
      $controller = new TipSessionController($view, $entityManager);

      // Inject the mocked dealerTipService by reflection
      $reflection = new ReflectionProperty($controller, 'dealerTipService');
      $reflection->setAccessible(true);
      $reflection->setValue($controller, $dealerTipService);

      // Inject the mocked serviceTipService by reflection
      $reflection = new ReflectionProperty($controller, 'serviceTipService');
      $reflection->setAccessible(true);
      $reflection->setValue($controller, $serviceTipService);

      // Inject the mocked sessionService by reflection
      $reflection = new ReflectionProperty($controller, 'sessionService');
      $reflection->setAccessible(true);
      $reflection->setValue($controller, $sessionService);

      return $controller;
  }

  public function getAListOfDealerTips($session)
  {
    return [ 
      new DealerTipSessionEntity(
        1,
        date_create('2019-06-26 19:05:00'),
        50,
        $session
      ),
      new DealerTipSessionEntity(
        2,
        date_create('2019-06-26 19:10:00'),
        60,
        $session
      ),
    ];
  }

  public function getAListOfServiceTips($session)
  {
    return [ 
      new ServiceTipSessionEntity(
        1,
        date_create('2019-06-26 19:05:00'),
        50,
        $session
      ),
      new ServiceTipSessionEntity(
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
    $dealerTipService = $this->createMock(DealerTipSessionService::class);
    $serviceTipService = $this->createMock(ServiceTipSessionService::class);
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

    $expectedDealerTips = $this->getAListOfDealerTips($expectedSession);
    $expectedServiceTips = $this->getAListOfServiceTips($expectedSession);

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $dealerTipService->method('fetchAll')->willReturn($expectedDealerTips);
    $serviceTipService->method('fetchAll')->willReturn($expectedServiceTips);

    $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession' => 2
    ];

    $expectedDatosUI = [];

    foreach ($expectedDealerTips as $dealerTip) {
      $dealerTips[] = $dealerTip->toArray();
    }

    foreach ($expectedServiceTips as $serviceTip) {
      $serviceTips[] = $serviceTip->toArray();
    }

    $expectedDatosUI['session']           = $expectedSession->toArray();
    $expectedDatosUI['session']['dealerTips'] = $dealerTips;
    $expectedDatosUI['session']['serviceTips'] = $serviceTips;
    $expectedDatosUI['breadcrumb']                 = 'Tips';

    $template    = 'tips.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->listAll($request, $response, $args);

  }

  public function testListDealerTip()
  {
    //for dealerTip

    $view = $this->createMock(Slim\Views\Twig::class);
    $dealerTipService = $this->createMock(DealerTipSessionService::class);
    $serviceTipService = $this->createMock(ServiceTipSessionService::class);
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

    $expectedDealerTip = new DealerTipSessionEntity(
      1,
      date_create('2019-06-26 19:05:00'),
      50,
      $expectedSession
    );

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $dealerTipService->method('fetchOne')->willReturn($expectedDealerTip);
    // $serviceTipService->method('fetchOne')->willReturn($expectedServiceTip);

    $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession'  => 2,
      'idDealerTip' => 1
    ];

    $expectedDatosUI = [];

    $expectedDatosUI['dealerTip'] = $expectedDealerTip->toArray();
    $expectedDatosUI['breadcrumb'] = 'Editar DealerTip';

    $template    = 'editDealerTip.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->list($request, $response, $args);

  }

  public function testListServiceTip()
  {
    //for serviceTip

    $view = $this->createMock(Slim\Views\Twig::class);
    $dealerTipService = $this->createMock(DealerTipSessionService::class);
    $serviceTipService = $this->createMock(ServiceTipSessionService::class);
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

    $expectedServiceTip = new ServiceTipSessionEntity(
      1,
      date_create('2019-06-26 19:05:00'),
      50,
      $expectedSession
    );

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $serviceTipService->method('fetchOne')->willReturn($expectedServiceTip);
    // $dealerTipService->method('fetchOne')->willReturn($expectedServiceTip);

    $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession'  => 2,
      'idServiceTip' => 1
    ];

    $expectedDatosUI = [];

    $expectedDatosUI['serviceTip'] = $expectedServiceTip->toArray();
    $expectedDatosUI['breadcrumb'] = 'Editar ServiceTip';

    $template    = 'editServiceTip.html.twig';

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

    $view              = $this->createMock(Slim\Views\Twig::class);
    $dealerTipService  = $this->createMock(DealerTipSessionService::class);
    $serviceTipService = $this->createMock(ServiceTipSessionService::class);
    $sessionService    = $this->createMock(SessionService::class);

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

    $expectedDealerTips = $this->getAListOfDealerTips($expectedSession);
    $expectedServiceTips = $this->getAListOfServiceTips($expectedSession);

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $dealerTipService->method('fetchAll')->willReturn($expectedDealerTips);
    $dealerTipService->method('update')->willReturn(true);
    $serviceTipService->method('fetchAll')->willReturn($expectedServiceTips);
    $serviceTipService->method('update')->willReturn(true);

    $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      [
        'tip to add',
        'id'         => 1,
        'idSession'  => 2,
        'hour'       => date_create('2019-06-26 19:05:00'),
        'dealerTip'  => 50,
        'serviceTip' => 60
      ]
    );

    $response = $this->createMock(Slim\Psr7\Response::class);

    $args = [
      'idSession' => 2
    ];

    $expectedDatosUI = [];

    foreach ($expectedDealerTips as $dealerTip) {
      $dealerTips[] = $dealerTip->toArray();
    }

    foreach ($expectedServiceTips as $serviceTip) {
      $serviceTips[] = $serviceTip->toArray();
    }

    $expectedDatosUI['session']                = $expectedSession->toArray();
    $expectedDatosUI['session']['dealerTips']  = $dealerTips;
    $expectedDatosUI['session']['serviceTips'] = $serviceTips;
    $expectedDatosUI['breadcrumb']             = 'Tips';
    $expectedDatosUI['message']                = ['El Dealer Tip se ingresó exitosamente.', 'El Service Tip se ingresó exitosamente.'];

    $template    = 'tips.html.twig';

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
    $dealerTipService = $this->createMock(DealerTipSessionService::class);
    $serviceTipService = $this->createMock(ServiceTipSessionService::class);
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

    $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $response = $this->createMock(Slim\Psr7\Response::class);
    $args = [
      'idSession'  => 2
    ];

    $expectedDatosUI = [];

    $expectedDatosUI['session']               = $expectedSession->toArray();
    $expectedDatosUI['breadcrumb']            = 'Nuevo Tip';

    $template    = 'newTips.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->form($request, $response, $args);

  }
  public function testUpdateDealerTip() // WhenIsAdded
  {

    $view              = $this->createMock(Slim\Views\Twig::class);
    $dealerTipService  = $this->createMock(DealerTipSessionService::class);
    $serviceTipService = $this->createMock(ServiceTipSessionService::class);
    $sessionService    = $this->createMock(SessionService::class);

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

    $expectedDealerTips = $this->getAListOfDealerTips($expectedSession);
    $expectedServiceTips = $this->getAListOfServiceTips($expectedSession);

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $dealerTipService->method('fetchAll')->willReturn($expectedDealerTips);
    $dealerTipService->method('update')->willReturn(true);
    $serviceTipService->method('fetchAll')->willReturn($expectedServiceTips);

    $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      [
        'tip to add',
        'id'         => 1,
        'idSession'  => 2,
        'hour'       => date_create('2019-06-26 19:05:00'),
        'dealerTip'  => 50,
        'serviceTip' => 60
      ]
    );

    $response = $this->createMock(Slim\Psr7\Response::class);

    $args = [
      'idSession'    => 2,
      'idDealerTip'  => 1
    ];

    $expectedDatosUI = [];

    foreach ($expectedDealerTips as $dealerTip) {
      $dealerTips[] = $dealerTip->toArray();
    }

    foreach ($expectedServiceTips as $serviceTip) {
      $serviceTips[] = $serviceTip->toArray();
    }

    $expectedDatosUI['session']                = $expectedSession->toArray();
    $expectedDatosUI['session']['dealerTips']  = $dealerTips;
    $expectedDatosUI['session']['serviceTips'] = $serviceTips;
    $expectedDatosUI['breadcrumb']             = 'Tips';
    $expectedDatosUI['message']                = ['El dealerTip se actualizó exitosamente.'];

    $template    = 'tips.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->update($request, $response, $args);
  }

  public function testUpdateServicerTip()
  {

    $view              = $this->createMock(Slim\Views\Twig::class);
    $dealerTipService  = $this->createMock(DealerTipSessionService::class);
    $serviceTipService = $this->createMock(ServiceTipSessionService::class);
    $sessionService    = $this->createMock(SessionService::class);

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

    $expectedDealerTips = $this->getAListOfDealerTips($expectedSession);
    $expectedServiceTips = $this->getAListOfServiceTips($expectedSession);

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $dealerTipService->method('fetchAll')->willReturn($expectedDealerTips);
    $serviceTipService->method('update')->willReturn(true);
    $serviceTipService->method('fetchAll')->willReturn($expectedServiceTips);

    $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      [
        'tip to add',
        'id'         => 1,
        'idSession'  => 2,
        'hour'       => date_create('2019-06-26 19:05:00'),
        'dealerTip'  => 50,
        'serviceTip' => 60
      ]
    );

    $response = $this->createMock(Slim\Psr7\Response::class);

    $args = [
      'idSession'    => 2,
      'idServiceTip' => 1 
    ];

    $expectedDatosUI = [];

    foreach ($expectedDealerTips as $dealerTip) {
      $dealerTips[] = $dealerTip->toArray();
    }

    foreach ($expectedServiceTips as $serviceTip) {
      $serviceTips[] = $serviceTip->toArray();
    }

    $expectedDatosUI['session']                = $expectedSession->toArray();
    $expectedDatosUI['session']['dealerTips']  = $dealerTips;
    $expectedDatosUI['session']['serviceTips'] = $serviceTips;
    $expectedDatosUI['breadcrumb']             = 'Tips';
    $expectedDatosUI['message']                = ['El serviceTip se actualizó exitosamente.'];

    $template    = 'tips.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->update($request, $response, $args);
  }

  public function testDeleteDealerTip() 
  {

    $view              = $this->createMock(Slim\Views\Twig::class);
    $dealerTipService  = $this->createMock(DealerTipSessionService::class);
    $serviceTipService = $this->createMock(ServiceTipSessionService::class);
    $sessionService    = $this->createMock(SessionService::class);

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

    $expectedDealerTips = $this->getAListOfDealerTips($expectedSession);
    $expectedServiceTips = $this->getAListOfServiceTips($expectedSession);

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $dealerTipService->method('fetchAll')->willReturn($expectedDealerTips);
    $dealerTipService->method('delete')->willReturn(true);
    $serviceTipService->method('fetchAll')->willReturn($expectedServiceTips);

    $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      [
        'tip to add',
        'id'         => 1,
        'idSession'  => 2,
        'hour'       => date_create('2019-06-26 19:05:00'),
        'dealerTip'  => 50,
        'serviceTip' => 60
      ]
    );
    $request->method('getQueryParams')->willReturn(
      [
        'idSession' => 2
      ]
    );

    $response = $this->createMock(Slim\Psr7\Response::class);

    $args = [
      'idSession'    => 2,
      'idDealerTip'  => 1
    ];

    $expectedDatosUI = [];

    foreach ($expectedDealerTips as $dealerTip) {
      $dealerTips[] = $dealerTip->toArray();
    }

    foreach ($expectedServiceTips as $serviceTip) {
      $serviceTips[] = $serviceTip->toArray();
    }

    $expectedDatosUI['session']                = $expectedSession->toArray();
    $expectedDatosUI['session']['dealerTips']  = $dealerTips;
    $expectedDatosUI['session']['serviceTips'] = $serviceTips;
    $expectedDatosUI['breadcrumb']             = 'Tips';
    $expectedDatosUI['message']                = ['El dealerTip se eliminó exitosamente'];

    $template    = 'tips.html.twig';

    $view->expects($this->once())
    ->method('render')
    ->with(
        $this->equalTo($response),
        $this->equalTo($template),
        $this->equalTo($expectedDatosUI),
    );

    $controller->delete($request, $response, $args);
  }

  public function testDeleteServiceTip() 
  {

    $view              = $this->createMock(Slim\Views\Twig::class);
    $dealerTipService  = $this->createMock(DealerTipSessionService::class);
    $serviceTipService = $this->createMock(ServiceTipSessionService::class);
    $sessionService    = $this->createMock(SessionService::class);

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

    $expectedDealerTips = $this->getAListOfDealerTips($expectedSession);
    $expectedServiceTips = $this->getAListOfServiceTips($expectedSession);

    $sessionService->method('fetchOne')->willReturn($expectedSession);
    $dealerTipService->method('fetchAll')->willReturn($expectedDealerTips);
    $serviceTipService->method('fetchAll')->willReturn($expectedServiceTips);
    $serviceTipService->method('delete')->willReturn(true);

    $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);
    $request = $this->createMock(Slim\Psr7\Request::class);
    $request->method('getParsedBody')->willReturn(
      [
        'tip to add',
        'id'         => 1,
        'idSession'  => 2,
        'hour'       => date_create('2019-06-26 19:05:00'),
        'dealerTip'  => 50,
        'serviceTip' => 60
      ]
    );
    $request->method('getQueryParams')->willReturn(
      [
        'idSession' => 2
      ]
    );

    $response = $this->createMock(Slim\Psr7\Response::class);

    $args = [
      'idSession'    => 2,
      'idServiceTip' => 1 
    ];

    $expectedDatosUI = [];

    foreach ($expectedDealerTips as $dealerTip) {
      $dealerTips[] = $dealerTip->toArray();
    }

    foreach ($expectedServiceTips as $serviceTip) {
      $serviceTips[] = $serviceTip->toArray();
    }

    $expectedDatosUI['session']                = $expectedSession->toArray();
    $expectedDatosUI['session']['dealerTips']  = $dealerTips;
    $expectedDatosUI['session']['serviceTips'] = $serviceTips;
    $expectedDatosUI['breadcrumb']             = 'Tips';
    $expectedDatosUI['message']                = ['El serviceTip se eliminó exitosamente'];

    $template    = 'tips.html.twig';

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