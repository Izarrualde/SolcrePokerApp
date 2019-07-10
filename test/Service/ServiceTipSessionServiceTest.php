<?php

use PHPUnit\Framework\TestCase;
use \Solcre\lmsuy\Entity\ServiceTipSessionEntity;
use \Solcre\lmsuy\Entity\SessionEntity;
use Solcre\lmsuy\Service\ServiceTipSessionService;
use Doctrine\ORM\EntityManager;
use \Solcre\lmsuy\Exception\ServiceTipInvalidException;
use Solcre\lmsuy\Repository\BaseRepository;

class ServiceTipSessionServiceTest extends TestCase
{

public function testAdd()
 {

    $data = [
      'id'        => 1, 
      'hour'      => '2019-07-04T19:00', 
      'serviceTip' => 100,
      'idSession' => 3
    ];

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);
   $mockedEntityManager->method('getReference')->willReturn(new SessionEntity(3));

    $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager);

    $expectedServiceTip = new ServiceTipSessionEntity();
    $expectedServiceTip->setHour(new \DateTime($data['hour']));
    $expectedServiceTip->setServiceTip($data['serviceTip']);
    $session = new SessionEntity(3);
    $expectedServiceTip->setSession($session);

   $mockedEntityManager->expects($this->once())
   ->method('persist')
   ->with(
       $this->equalTo($expectedServiceTip)
   )/*->willReturn('anything')*/;

   $serviceTipSessionService->add($data);
 // y que se llame metodo flush con anythig
}

public function testUpdate()
 {

    $data = [
      'id'        => 1, 
      'hour'      => '2019-07-04T19:00', 
      'serviceTip' => 100,
      'idSession' => 3
    ];

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);

    $mockedRepository = $this->createMock(BaseRepository::class);
    $mockedRepository->method('find')->willReturn(new ServiceTipSessionEntity(
    1,
    new \DateTime('2019-07-04T15:00'),
    80,
    new SessionEntity(3)
    )
   );

    $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

    $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager);

    $expectedServiceTip    = new ServiceTipSessionEntity();
    $expectedServiceTip->setId(1);
    $expectedServiceTip->setHour(new \DateTime($data['hour']));
    $expectedServiceTip->setServiceTip($data['serviceTip']);
    $session = new SessionEntity(3);
    $expectedServiceTip->setSession($session);

   $mockedEntityManager->expects($this->once())
   ->method('persist')
   ->with(
       $this->equalTo($expectedServiceTip)
   );

   $serviceTipSessionService->update($data);
 // y que se llame metodo flush con anythig

 }

  public function testDelete()
  {
    $data = [
      'id' => 1
    ];

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);
   $mockedEntityManager->method('getReference')->willReturn(new ServiceTipSessionEntity(1));

   $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager);

   $expectedServiceTip = new ServiceTipSessionEntity($data['id']);

   $mockedEntityManager->expects($this->once())
   ->method('remove')
   ->with(
       $this->equalTo($expectedServiceTip)
   )/*->willReturn('anything')*/;

   $serviceTipSessionService->delete($data['id']);

  }
}