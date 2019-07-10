<?php

use PHPUnit\Framework\TestCase;
use \Solcre\lmsuy\Entity\DealerTipSessionEntity;
use \Solcre\lmsuy\Entity\SessionEntity;
use Solcre\lmsuy\Service\DealerTipSessionService;
use Doctrine\ORM\EntityManager;
use \Solcre\lmsuy\Exception\DealerTipInvalidException;
use Solcre\lmsuy\Repository\BaseRepository;

class DealerTipSessionServiceTest extends TestCase
{

public function testAdd()
 {

    $data = [
      'id'        => 1, 
      'hour'      => '2019-07-04T19:00', 
      'dealerTip' => 100,
      'idSession' => 3
    ];

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);
   $mockedEntityManager->method('getReference')->willReturn(new SessionEntity(3));

    $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager);

    $expectedDealerTip    = new DealerTipSessionEntity();
    $expectedDealerTip->setHour(new \DateTime($data['hour']));
    $expectedDealerTip->setDealerTip($data['dealerTip']);
    $session = new SessionEntity(3);
    $expectedDealerTip->setSession($session);

   $mockedEntityManager->expects($this->once())
   ->method('persist')
   ->with(
       $this->equalTo($expectedDealerTip)
   )/*->willReturn('anything')*/;

   $dealerTipSessionService->add($data);
 // y que se llame metodo flush con anythig

 }

public function testUpdate()
 {

    $data = [
      'id'        => 1, 
      'hour'      => '2019-07-04T19:00', 
      'dealerTip' => 100,
      'idSession' => 3
    ];

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);

    $mockedRepository = $this->createMock(BaseRepository::class);
    $mockedRepository->method('find')->willReturn(new DealerTipSessionEntity(
    1,
    new \DateTime('2019-07-04T15:00'),
    80,
    new SessionEntity(3)
    )
   );

    $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

    $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager);

    $expectedDealerTip    = new DealerTipSessionEntity();
    $expectedDealerTip->setId(1);
    $expectedDealerTip->setHour(new \DateTime($data['hour']));
    $expectedDealerTip->setDealerTip($data['dealerTip']);
    $session = new SessionEntity(3);
    $expectedDealerTip->setSession($session);

   $mockedEntityManager->expects($this->once())
   ->method('persist')
   ->with(
       $this->equalTo($expectedDealerTip)
   );

   $dealerTipSessionService->update($data);
 // y que se llame metodo flush con anythig

 }

  public function testDelete()
  {
    $data = [
      'id' => 1
    ];

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);
   $mockedEntityManager->method('getReference')->willReturn(new DealerTipSessionEntity(1));

   $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager);

   $expectedDealerTip = new DealerTipSessionEntity($data['id']);

   $mockedEntityManager->expects($this->once())
   ->method('remove')
   ->with(
       $this->equalTo($expectedDealerTip)
   )/*->willReturn('anything')*/;

   $dealerTipSessionService->delete($data['id']);

  }
}