<?php

use PHPUnit\Framework\TestCase;
// use ReflectionMethod;
use Solcre\lmsuy\Entity\ComissionSessionEntity;
use Solcre\lmsuy\Entity\SessionEntity;

class ComisisonSessionEntityTest extends TestCase
{

  public function testCreateWithParams()
  {
    $id = 1;
    $hour = date_create('2019-06-26 19:00:00');
    $amount = 25;
    $session = new SessionEntity();

    $comission = new ComissionSessionEntity(
      $id,
      $hour,
      $amount,
      $session
    );

    $this->assertEquals($id, $comission->getId());
    $this->assertEquals($hour, $comission->getHour());
    $this->assertEquals($amount, $comission->getComission());
    $this->assertSame($session, $comission->getSession());
  }

  public function testGetSessionUndefined() {
    $comission = new ComissionSessionEntity();

    $this->assertEquals(null, $comission->getSession());
  }

  public function testToArray()
  {

    $session = new SessionEntity();
    $session->setId(2);

    $comission = new ComissionSessionEntity();
    $comission->setId(1);
    $comission->setSession($session);
    $comission->setHour(date_create('2019-06-26 19:00:00'));
    $comission->setComission(100);


    $expectedArray = [
      'id'        => 1,
      'idSession' => 2,
      'hour'      => date_create('2019-06-26 19:00:00'),
      'comission' => 100
    ];  

    $this->assertEquals($expectedArray, $comission->toArray());
  }
}
