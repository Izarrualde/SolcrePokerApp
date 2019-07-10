<?php

use PHPUnit\Framework\TestCase;
use \Solcre\lmsuy\Entity\BuyinSessionEntity;
use \Solcre\lmsuy\Entity\UserSessionEntity;
use Solcre\lmsuy\Service\BuyinSessionService;
use Solcre\lmsuy\Service\UserSessionService;
use Solcre\lmsuy\Service\UserService;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\Exception\BuyinInvalidException;
use Solcre\lmsuy\Repository\BaseRepository;

class BuyinSessionServiceTest extends TestCase
{

public function testAdd()
 {
    $data = [
        'id'            =>  1,
        'amountCash'    => 50,
        'amountCredit'  => 60,
        'idUserSession' => null,
        'hour'          => '2019-07-04T19:00',
        'currency'      => 1,
        'approved'      => 1
    ];

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);
   $mockedUserSessionService = $this->createMock(UserSessionService::class);
   $mockedUserSessionService->method('fetch')->willReturn(new UserSessionEntity(1));

   $buyinSessionService = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService);

    $expectedBuyin    = new BuyinSessionEntity();
    $expectedBuyin->setHour(new \DateTime($data['hour']));
    $expectedBuyin->setAmountCash($data['amountCash']);
    $expectedBuyin->setAmountCredit($data['amountCredit']);
    $expectedBuyin->setCurrency($data['currency']);
    $expectedBuyin->setIsApproved($data['approved']);
    $userSession = new UserSessionEntity(1);
    $userSession->setStart(new \DateTime($data['hour']));
    $expectedBuyin->setUserSession($userSession);
  

   $mockedEntityManager->expects($this->exactly(2))
   ->method('persist')
   ->withConsecutive(
      $this->equalTo($userSession),
      $this->equalTo($expectedBuyin)
   );

   $buyinSessionService->add($data);
 // y que se llame metodo flush con anythig
 }

public function testUpdate()
 {

    $data = [
        'id'            =>  1,
        'amountCash'    => 50,
        'amountCredit'  => 60,
        'idUserSession' => null,
        'hour'          => '2019-07-04T19:00',
        'currency'      => 1,
        'approved'      => 1
    ];

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);


    $mockedRepository = $this->createMock(BaseRepository::class);
    $mockedRepository->method('find')->willReturn(
      new BuyinSessionEntity(
        1,
        10,
        20,
        new UserSessionEntity(1),
        new \DateTime('2019-07-04T12:00'),
        1,
        1
      )
    );

    $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

  $mockedUserSessionService = $this->createMock(UserSessionService::class);

   $buyinSessionService = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService);

    $expectedBuyin    = new BuyinSessionEntity();
    $expectedBuyin->setId(1);
    $expectedBuyin->setUserSession(new UserSessionEntity(1));
    $expectedBuyin->setHour(new \DateTime($data['hour']));
    $expectedBuyin->setAmountCash($data['amountCash']);
    $expectedBuyin->setAmountCredit($data['amountCredit']);
    $expectedBuyin->setIsApproved($data['approved']);
    $expectedBuyin->setCurrency($data['currency']);
  

   $mockedEntityManager->expects($this->once())
   ->method('persist')
   ->with(
       $this->equalTo($expectedBuyin)
   )/*->willReturn('anything')*/;

   $buyinSessionService->update($data);
 // y que se llame metodo flush con anythig
 }


  public function testDelete()
  {
    $data = [
      'id' => 1
    ];

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);
   $mockedEntityManager->method('getReference')->willReturn(new BuyinSessionEntity(1));

   $mockedUserService = new UserService($mockedEntityManager);

   $mockedUserSessionService = new UserSessionService($mockedEntityManager, $mockedUserService);

   $buyinSessionService = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService);

   $expectedBuyin = new BuyinSessionEntity($data['id']);

   $mockedEntityManager->expects($this->once())
   ->method('remove')
   ->with(
       $this->equalTo($expectedBuyin)
   )/*->willReturn('anything')*/;

   $buyinSessionService->delete($data['id']);

  }
}