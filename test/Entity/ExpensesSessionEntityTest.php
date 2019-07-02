<?php

use PHPUnit\Framework\TestCase;
// use ReflectionMethod;
use Solcre\lmsuy\Entity\ExpensesSessionEntity;
use Solcre\lmsuy\Entity\SessionEntity;

class ExpensesSessionEntityTest extends TestCase
{

  public function testCreateWithParams()
  {
    $id = 1;
    $description = 'comida';
    $amount = 25;
    $session = new SessionEntity();

    $expenditure = new ExpensesSessionEntity(
      $id,
      $session,
      $description,
      $amount
    );

    $this->assertEquals($id, $expenditure->getId());
    $this->assertSame($session, $expenditure->getSession());
    $this->assertEquals($description, $expenditure->getDescription());
    $this->assertEquals($amount, $expenditure->getAmount());
  }

  public function testToArray()
  {

    $session = new SessionEntity();
    $session->setId(2);

    $expenditure = new ExpensesSessionEntity();
    $expenditure->setId(1);
    $expenditure->setSession($session);
    $expenditure->setDescription('comida');
    $expenditure->setAmount(100);


    $expectedArray = [
      'id'          => 1,
      'idSession'   => 2,
      'description' => 'comida',
      'amount'      => 100
    ];  

    $this->assertEquals($expectedArray, $expenditure->toArray());
  }
}