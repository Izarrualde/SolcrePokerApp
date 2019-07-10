<?php

use PHPUnit\Framework\TestCase;
use \Solcre\lmsuy\Entity\UserSessionEntity;
use \Solcre\lmsuy\Entity\UserEntity;
use \Solcre\lmsuy\Entity\SessionEntity;
use Solcre\lmsuy\Service\UserSessionService;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\Exception\UserSessionAlreadyAddedException;
use Solcre\lmsuy\Repository\BaseRepository;

class UserSessionServiceTest extends TestCase
{


public function testAdd()
 {

    $data = [
      'idSession'  => 3,
      'idUser'     => 1,
      'isApproved' => 1,
      'points'     => 0,
      'start'      => '2019-07-04T19:00'
    ];

  $map = [
    ['Solcre\lmsuy\Entity\UserEntity', $data['idUser'], new UserEntity(1)],
    ['Solcre\lmsuy\Entity\SessionEntity', $data['idSession'], new SessionEntity(3)]
  ];  

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);
   $mockedEntityManager->method('getReference')->will($this->returnValueMap($map));



    $session = new SessionEntity(3);
    $user = new UserEntity(1);
    $userSessionService = new UserSessionService($mockedEntityManager);

    $expectedUserSession    = new UserSessionEntity();
    $expectedUserSession->setSession($session);
    $expectedUserSession->setUser($user);
    $expectedUserSession->setIdUser($data['idUser']);
    $expectedUserSession->setIsApproved($data['isApproved']);
    $expectedUserSession->setAccumulatedPoints($data['points']);
    $expectedUserSession->setStart(new \DateTime($data['start']));

   $mockedEntityManager->expects($this->once())
   ->method('persist')
   ->with(
       $this->equalTo($expectedUserSession)
   )/*->willReturn('anything')*/;

   $userSessionService->add($data);
 // y que se llame metodo flush con anythig

 }
/*
 public function testAddWithException()
 {

    $data = [
      'idSession'  => 3,
      'idUser'     => 1,
      'isApproved' => 1,
      'points'     => 0,
      'start'      => '2019-07-04T19:00'
    ];

  $map = [
    ['Solcre\lmsuy\Entity\UserEntity', $data['idUser'], new UserEntity(1)],
    ['Solcre\lmsuy\Entity\UserEntity', $data['idUser'], new UserEntity(1)],
    ['Solcre\lmsuy\Entity\SessionEntity', $data['idSession'], new SessionEntity(3)]
  ];  

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);
   $mockedEntityManager->method('getReference')->will($this->returnValueMap($map));

   
   $mockedSessionEntity = $this->createMock(SessionEntity::class);
   $mockedSessionEntity->method('getActivePlayers')->willReturn(['1']);


    $session = new SessionEntity(3);
    $user = new UserEntity(1);
    $userSessionService = new UserService($mockedEntityManager);

    $expectedUserSession    = new UserEntity();
    $expectedUserSession->setSession($session);
    $expectedUserSession->setUser($user);
    $expectedUserSession->setIdUser($data['idUser']);
    $expectedUserSession->setIsApproved($data['isApproved']);
    $expectedUserSession->setAccumulatedPoints(new \DateTime($data['points']));


  //al llamar a userSessionService con esta $data, corroboro que se lanza UserSessionAlreadyAddedException 
  // ver en siguientes lineas como verificar que userSessionService->add lanzo la expcepcion.  

   $mockedEntityManager->expects($this->once())
   ->method('persist')
   ->with(
       $this->contains([$exception->getMessage()])
   )/*->willReturn('anything')*//*;

   $userSessionService->add($data);



 // y que se llame metodo flush con anythig

 }
*/

 public function testUpdate()
 {

    $data = [
      'id'         => 1,
      'idSession'  => 3,
      'idUser'     => 1,
      'isApproved' => 1,
      'points'     => 0,
      'cashout'    => 0,
      'start'      => '2019-07-04T19:00',
      'end'        =>  null
    ];

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);
   $mockedEntityManager->method('getReference')->willReturn(new SessionEntity(3));

    $mockedRepository = $this->createMock(BaseRepository::class);
    $mockedRepository->method('find')->willReturn(
      new UserSessionEntity(
        1,
        new SessionEntity(3),
        1,
        1,
        0,
        0,
        new \DateTime('2019-07-04T18:00'),
        null,
        new UserEntity(1)
    )
   );


    $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

    $userSessionService = new UserSessionService($mockedEntityManager);

    $expectedUserSession    = new UserSessionEntity();
    $expectedUserSession->setId($data['id']);
    $expectedUserSession->setSession(new SessionEntity($data['idSession']));
    $expectedUserSession->setUser(new UserEntity($data['idUser']));
    $expectedUserSession->setIdUser($data['idUser']);
    $expectedUserSession->setIsApproved($data['isApproved']);
    $expectedUserSession->setAccumulatedPoints($data['points']);
    $expectedUserSession->setCashout($data['cashout']);
    $expectedUserSession->setStart(new \DateTime($data['start']));

   $mockedEntityManager->expects($this->once())
   ->method('persist')
   ->with(
       $this->equalTo($expectedUserSession)
   )/*->willReturn('anything')*/;

   $userSessionService->update($data);
 // y que se llame metodo flush con anythig

 }



  public function testDelete()
  {
    $data = [
      'id' => 1
    ];

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);
   $mockedEntityManager->method('getReference')->willReturn(new UserSessionEntity(1));

   $userSessionService = new UserSessionService($mockedEntityManager);

   $expectedUserSession = new UserSessionEntity($data['id']);

   $mockedEntityManager->expects($this->once())
   ->method('remove')
   ->with(
       $this->equalTo($expectedUserSession)
   )/*->willReturn('anything')*/;

   $userSessionService->delete($data['id']);

  }
}