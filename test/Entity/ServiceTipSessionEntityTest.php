<?php

use PHPUnit\Framework\TestCase;
// use ReflectionMethod;
use Solcre\lmsuy\Entity\ServiceTipSessionEntity;
use Solcre\lmsuy\Entity\SessionEntity;

class ServiceTipSessionEntityTest extends TestCase
{

  public function testCreateWithParams()
  {
    $id = 1;
    $hour = date_create('2019-06-26 19:00:00');
    $amount = 25;
    $session = new SessionEntity();

    $serviceTip = new ServiceTipSessionEntity(
      $id,
      $hour,
      $amount,
      $session
    );

    $this->assertEquals($id, $serviceTip->getId());
    $this->assertEquals($hour, $serviceTip->getHour());
    $this->assertEquals($amount, $serviceTip->getServiceTip());
    $this->assertSame($session, $serviceTip->getSession());
  }

  public function testToArray()
  {

    $session = new SessionEntity();
    $session->setId(2);

    $serviceTip = new ServiceTipSessionEntity();
    $serviceTip->setId(1);
    $serviceTip->setSession($session);
    $serviceTip->setHour(date_create('2019-06-26 19:00:00'));
    $serviceTip->setServiceTip(100);


    $expectedArray = [
      'id'        => 1,
      'idSession' => 2,
      'hour'      => date_create('2019-06-26 19:00:00'),
      'serviceTip' => 100
    ];  

    $this->assertEquals($expectedArray, $serviceTip->toArray());
  }
}