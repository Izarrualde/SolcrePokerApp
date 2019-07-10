<?php

use PHPUnit\Framework\TestCase;
use \Solcre\lmsuy\Entity\ExpensesSessionEntity;
use \Solcre\lmsuy\Entity\SessionEntity;
use Solcre\lmsuy\Service\ExpensesSessionService;
use Doctrine\ORM\EntityManager;
use \Solcre\lmsuy\Exception\ExpensesInvalidException;
use Solcre\lmsuy\Repository\BaseRepository;

class ExpensesSessionServiceTest extends TestCase
{

public function testAdd()
 {

    $data = [
      'id'          => 1, 
      'description' => 'gasto de sesion', 
      'amount'      => 100,
      'idSession'   => 3
    ];

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);
   $mockedEntityManager->method('getReference')->willReturn(new SessionEntity(3));

    $expensesSessionService = new ExpensesSessionService($mockedEntityManager);

    $expectedExpenditure    = new ExpensesSessionEntity();
    $expectedExpenditure->setDescription($data['description']);
    $expectedExpenditure->setAmount($data['amount']);
    $session = new SessionEntity(3);
    $expectedExpenditure->setSession($session);

   $mockedEntityManager->expects($this->once())
   ->method('persist')
   ->with(
       $this->equalTo($expectedExpenditure)
   )/*->willReturn('anything')*/;

   $expensesSessionService->add($data);
 // y que se llame metodo flush con anythig

 }
public function testUpdate()
 {
    $data = [
      'id'          => 1, 
      'description' => 'gasto de sesion', 
      'amount'      => 100,
      'idSession'   => 3
    ];

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);

   $mockedRepository = $this->createMock(BaseRepository::class);
   $mockedRepository->method('find')->willReturn(new ExpensesSessionEntity(
    1,
    new SessionEntity(3),
    'description original',
    50    
    )
   );

   $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

    $expensesSessionService = new ExpensesSessionService($mockedEntityManager);

    $expectedExpenditure    = new ExpensesSessionEntity();
    $expectedExpenditure->setId(1);
    $expectedExpenditure->setDescription($data['description']);
    $expectedExpenditure->setAmount($data['amount']);
    $expectedExpenditure->setSession(new SessionEntity(3));

   $mockedEntityManager->expects($this->once())
   ->method('persist')
   ->with(
       $this->equalTo($expectedExpenditure)
   );

   $expensesSessionService->update($data);
 // y que se llame metodo flush con anythig

 }


  public function testDelete()
  {
    $data = [
      'id' => 1
    ];

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);
   $mockedEntityManager->method('getReference')->willReturn(new ExpensesSessionEntity(1));

   $expensesSessionService = new ExpensesSessionService($mockedEntityManager);

   $expectedExpenditure = new ExpensesSessionEntity($data['id']);

   $mockedEntityManager->expects($this->once())
   ->method('remove')
   ->with(
       $this->equalTo($expectedExpenditure)
   )/*->willReturn('anything')*/;

   $expensesSessionService->delete($data['id']);

  }
}