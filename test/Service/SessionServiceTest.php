<?php

use PHPUnit\Framework\TestCase;
use \Solcre\lmsuy\Entity\SessionEntity;
use Solcre\lmsuy\Service\SessionService;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\Repository\BaseRepository;

class SessionServiceTest extends TestCase
{

public function testAdd()
 {
    $data = [
      'id'            => 1, 
      'hour'          => '2019-07-04T19:00', 
      'comission'     => 100,
      'idSession'     => 3,
      'date'          => '2019-07-04',
      'startTime'     => '2019-07-04T19:00',
      'startTimeReal' => '2019-07-04T19:15',
      'endTime'       => '2019-07-04T20:00',
      'title'         => 'mesa mixta',
      'description'   => 'lunes',
      'seats'         => 9
    ];

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);

   $sessionService = new SessionService($mockedEntityManager);

    $expectedSession    = new SessionEntity();
    $expectedSession->setDate(new \DateTime($data['date']));
    $expectedSession->setStartTime(new \DateTime($data['startTime']));
    $expectedSession->setStartTimeReal(new \DateTime($data['startTimeReal']));
    $expectedSession->setEndTime(new \DateTime($data['endTime']));

    $expectedSession->setTitle($data['title']);
    $expectedSession->setDescription($data['description']);
    $expectedSession->setSeats($data['seats']);
 
   $mockedEntityManager->expects($this->once())
   ->method('persist')
   ->with(
       $this->equalTo($expectedSession)
   )/*->willReturn('anything')*/;

   $sessionService->add($data);
 // y que se llame metodo flush con anythig

 }

 public function testUpdate()
 {
    $data = [
      'id'            => 1, 
      'hour'          => '2019-07-04T19:00', 
      'comission'     => 100,
      'idSession'     => 3,
      'date'          => '2019-07-04',
      'startTime'     => '2019-07-04T19:00',
      'startTimeReal' => '2019-07-04T19:15',
      'endTime'       => '2019-07-04T20:00',
      'title'         => 'title actualizado',
      'description'   => 'desscription actualizada',
      'seats'         => 9
    ];

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);


    $mockedRepository = $this->createMock(BaseRepository::class);
    $mockedRepository->method('find')->willReturn(
      new SessionEntity(
      1,
      new \DateTime('2019-07-04T15:00'),
      'title original',
      'description original',
      'photo original',
      9,
      new \DateTime('2019-07-04T18:00'),
      new \DateTime('2019-07-04T18:30'),
      null
    )
   );

   $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

   $sessionService = new SessionService($mockedEntityManager);

    $expectedSession    = new SessionEntity();
    $expectedSession->setId($data['id']);
    $expectedSession->setDate(new \DateTime($data['date']));
    $expectedSession->setTitle($data['title']);
    $expectedSession->setDescription($data['description']);
    $expectedSession->setSeats($data['seats']);
    $expectedSession->setPhoto('photo original');
    $expectedSession->setStartTime(new \DateTime($data['startTime']));
    $expectedSession->setStartTimeReal(new \DateTime($data['startTimeReal']));
    $expectedSession->setEndTime(new \DateTime($data['endTime']));

   $mockedEntityManager->expects($this->once())
   ->method('persist')
   ->with(
       $this->equalTo($expectedSession)
   )/*->willReturn('anything')*/;

   $sessionService->update($data);
 // y que se llame metodo flush con anythig

 }


}