<?php

use PHPUnit\Framework\TestCase;
// use ReflectionMethod;
use Solcre\lmsuy\Entity\DealerTipSessionEntity;
use Solcre\lmsuy\Entity\SessionEntity;

class DealerSessionEntityTest extends TestCase
{

  public function testCreateWithParams()
  {
    $id = 1;
    $hour = date_create('2019-06-26 19:00:00');
    $amount = 25;
    $session = new SessionEntity();

    $dealerTip = new DealerTipSessionEntity(
      $id,
      $hour,
      $amount,
      $session
    );

    $this->assertEquals($id, $dealerTip->getId());
    $this->assertEquals($hour, $dealerTip->getHour());
    $this->assertEquals($amount, $dealerTip->getDealerTip());
    $this->assertSame($session, $dealerTip->getSession());
  }

  public function testToArray()
  {

    $session = new SessionEntity();
    $session->setId(2);

    $dealerTip = new DealerTipSessionEntity();
    $dealerTip->setId(1);
    $dealerTip->setSession($session);
    $dealerTip->setHour(date_create('2019-06-26 19:00:00'));
    $dealerTip->setDealerTip(100);


    $expectedArray = [
      'id'        => 1,
      'idSession' => 2,
      'hour'      => date_create('2019-06-26 19:00:00'),
      'dealerTip' => 100
    ];  

    $this->assertEquals($expectedArray, $dealerTip->toArray());
  }
}