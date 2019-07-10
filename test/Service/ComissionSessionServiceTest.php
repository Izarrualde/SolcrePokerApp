<?php

use PHPUnit\Framework\TestCase;
use Solcre\lmsuy\Entity\ComissionSessionEntity;
use Solcre\lmsuy\Entity\SessionEntity;
use Solcre\lmsuy\Service\ComissionSessionService;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\Exception\ComissionInvalidException;
use Solcre\lmsuy\Service\BaseService;
use Solcre\lmsuy\Repository\BaseRepository;

class ComissionSessionServiceTest extends TestCase
{
/*
  public function createService() {
      $container = AppWrapper::getContainer();

      // Get EntityManager from container
      $entityManager = $container->get(EntityManager::class);

      $viewMock = 
      $comissionSessionService = new ComissionSessionService($entityManager);

      // Inject the mocked comissionService by reflection
      $reflection = new ReflectionProperty($comissionSessionService, 'comissionSessionService');
      $reflection->setAccessible(true);
      $reflection->setValue($comissionSessionService, $entityManager);

      return $comissionSessionService;
  }
*/
public function testAdd()
 {

    $data = [
      'id'        => 1, 
      'hour'      => '2019-07-04T19:00', 
      'comission' => 100,
      'idSession' => 3
    ];

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);
   $mockedEntityManager->method('getReference')->willReturn(new SessionEntity(3));

   $comissionSessionService = new ComissionSessionService($mockedEntityManager);

    $expectedComission    = new ComissionSessionEntity();
    $expectedComission->setHour(new \DateTime($data['hour']));
    $expectedComission->setComission($data['comission']);
    $session = new SessionEntity(3);
    $expectedComission->setSession($session);

   $mockedEntityManager->expects($this->once())
   ->method('persist')
   ->with(
       $this->equalTo($expectedComission)
   )/*->willReturn('anything')*/;

   $comissionSessionService->add($data);
 // y que se llame metodo flush con anythig

 }


  public function testUpdate()
 {

    $data = [
      'id'        => 1, 
      'hour'      => '2019-07-04T19:00', 
      'comission' => 100,
      'idSession' => 3
    ];

    $mockedEntityManager = $this->createMock(EntityManager::class);
    $mockedEntityManager->method('persist')->willReturn(true);

    $mockedRepository = $this->createMock(BaseRepository::class);
    $mockedRepository->method('find')->willReturn(
      new ComissionSessionEntity(
        1,
        new \DateTime('2019-07-04T12:00'),
        80,
        new SessionEntity(3)
      )
    );


  $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

   $comissionSessionService = new ComissionSessionService($mockedEntityManager);

    $expectedComission    = new ComissionSessionEntity();
    $expectedComission->setId(1);
    $expectedComission->setHour(new \DateTime($data['hour']));
    $expectedComission->setComission($data['comission']);
    $session = new SessionEntity(3);
    $expectedComission->setSession($session);

   $mockedEntityManager->expects($this->once())
   ->method('persist')
   ->with(
       $this->equalTo($expectedComission)
   );

   $comissionSessionService->update($data);
 // y que se llame metodo flush con anythig

 }


  public function testDelete()
  {
    $data = [
      'id'        => 1
    ];

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);
   $mockedEntityManager->method('getReference')->willReturn(new ComissionSessionEntity(1));

   $comissionSessionService = new ComissionSessionService($mockedEntityManager);


   $expectedComission = new ComissionSessionEntity($data['id']);

   $mockedEntityManager->expects($this->once())
   ->method('remove')
   ->with(
       $this->equalTo($expectedComission)
   )/*->willReturn('anything')*/;

   $comissionSessionService->delete($data['id']);

  }

}