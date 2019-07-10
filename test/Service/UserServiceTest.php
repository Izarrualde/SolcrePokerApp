<?php

use PHPUnit\Framework\TestCase;
use \Solcre\lmsuy\Entity\UserEntity;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\Service\UserService;
use Solcre\lmsuy\Exception\UserHadActionException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Solcre\lmsuy\Repository\BaseRepository;

class UserServiceTest extends TestCase
{

public function testAdd()
 {

    $data = [
      'password'   => '123',
      'firstname'  => 'Jhon',
      'lastname'   => 'Doe',
      'email'      => 'jhon@lmsuy.com',
      'username'   => '12345',
      'multiplier' => 0,
      'active'     => 1,
      'hours'      => 0,
      'points'     => 0,
      'sessions'   => 0,
      'results'    => 0,
      'cashin'     => 0
    ];

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);

   $userService = new UserService($mockedEntityManager);

    $expectedUser    = new UserEntity();
    $expectedUser->setPassword($data['password']);
    $expectedUser->setName($data['firstname']);
    $expectedUser->setLastname($data['lastname']);
    $expectedUser->setEmail($data['email']);
    $expectedUser->setUsername($data['username']);
    $expectedUser->setMultiplier($data['multiplier']);
    $expectedUser->setIsActive($data['active']);
    $expectedUser->setHours($data['hours']);
    $expectedUser->setPoints($data['points']);
    $expectedUser->setSessions($data['sessions']);
    $expectedUser->setResults($data['results']);
    $expectedUser->setCashin($data['cashin']);

   $mockedEntityManager->expects($this->once())
   ->method('persist')
   ->with(
       $this->equalTo($expectedUser)
   )/*->willReturn('anything')*/;

   $userService->add($data);
 // y que se llame metodo flush con anythig

 }

public function testUpdate()
 {

    $data = [
      'id'         =>1,
      'password'   => '123',
      'name'  => 'Jhon',
      'lastname'   => 'Doe',
      'email'      => 'jhon@lmsuy.com',
      'username'   => '12345',
      'multiplier' => 0,
      'active'     => 1,
      'hours'      => 0,
      'points'     => 0,
      'sessions'   => 0,
      'results'    => 0,
      'cashin'     => 0
    ];

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);

  $mockedRepository = $this->createMock(BaseRepository::class);
  $mockedRepository->method('find')->willReturn(
      new UserEntity(
    1,
    '123',
    null,
    'origina@email.com',
    'original lastname',
    'original name',
    'original username',
    0,
    1,
    0,
    0,
    0,
    0,
    0
    )
   );


  $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

   $userService = new UserService($mockedEntityManager);

    $expectedUser    = new UserEntity();
    $expectedUser->setId($data['id']);
    $expectedUser->setPassword($data['password']);
    $expectedUser->setName($data['name']);
    $expectedUser->setLastname($data['lastname']);
    $expectedUser->setEmail($data['email']);
    $expectedUser->setUsername($data['username']);
    $expectedUser->setMultiplier($data['multiplier']);
    $expectedUser->setIsActive($data['active']);
    $expectedUser->setHours($data['hours']);
    $expectedUser->setPoints($data['points']);
    $expectedUser->setSessions($data['sessions']);
    $expectedUser->setResults($data['results']);
    $expectedUser->setCashin($data['cashin']);

   $mockedEntityManager->expects($this->once())
   ->method('persist')
   ->with(
       $this->equalTo($expectedUser)
   )/*->willReturn('anything')*/;

   $userService->update($data);
 // y que se llame metodo flush con anythig

 }

  public function testDelete()
  {
    $data = [
      'id' => 1
    ];

   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);

   $mockedEntityManager->method('getReference')->willReturn(new UserEntity(1));

   $userService = new UserService($mockedEntityManager);

   $expectedUser = new UserEntity($data['id']);

   $mockedEntityManager->expects($this->once())
   ->method('remove')
   ->with(
       $this->equalTo($expectedUser)
   )/*->willReturn('anything')*/;

   $userService->delete($data['id']);

  }
}