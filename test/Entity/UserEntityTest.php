<?php

use PHPUnit\Framework\TestCase;
// use ReflectionMethod;
use Solcre\lmsuy\Entity\UserEntity;

class UserEntityTest extends TestCase
{

  public function testCreateWithParams()
  {
    $id = 1;
    $password = '1234';
    $mobile = null;
    $email = 'user@lmsuy.com';
    $lastname = 'Rod';
    $name = 'Diego';
    $username = '1234';
    $multiplier = 1;
    $isActive = 0;
    $hours = 0;
    $points = 0;
    $sessions = 0;
    $results = 0;
    $cashin = 0;

    $user = new UserEntity(
      $id,
      $password,
      $mobile,
      $email,
      $lastname,
      $name,
      $username,
      $multiplier,
      $isActive,
      $hours,
      $points,
      $sessions,
      $results,
      $cashin
    );

    $this->assertEquals($id, $user->getId());
    $this->assertEquals($password, $user->getPassword());
    $this->assertEquals($mobile, $user->getMobile());
    $this->assertEquals($email, $user->getEmail());
    $this->assertEquals($lastname, $user->getLastname());
    $this->assertEquals($name, $user->getName());
    $this->assertEquals($username, $user->getUsername());
    $this->assertEquals($multiplier, $user->getMultiplier());
    $this->assertEquals($isActive, $user->getIsActive());
    $this->assertEquals($hours, $user->getHours());
    $this->assertEquals($points, $user->getPoints());
    $this->assertEquals($sessions, $user->getSessions());
    $this->assertEquals($results, $user->getResults());
    $this->assertEquals($cashin, $user->getCashin());
  }

  public function testToArray()
  {
    $user = new UserEntity();
    $user->setId(1);
    $user->setPassword(123);
    $user->setMobile(1234);
    $user->setEmail('diego@lmsuy.com');
    $user->setLastname('Rod');
    $user->setName('Diego');
    $user->setUsername(12345);
    $user->setMultiplier(1);
    $user->setIsActive(1);
    $user->setHours(0);
    $user->setPoints(0);
    $user->setSessions(0);
    $user->setResults(0);
    $user->setCashin(0);

    $expectedArray = [
        'id'         => 1,
        'password'   => 123,
        'mobile'     => 1234,
        'email'      => 'diego@lmsuy.com',
        'name'       => 'Diego',
        'lastname'   => 'Rod',
        'username'   => 12345,
        'multiplier' => 1,
        'sessions'   => 0,
        'isActive'   => 1,
        'hours'      => 0,
        'points'     => 0,
        'results'    => 0,
        'cashin'     => 0
        ];

    $this->assertEquals($expectedArray, $user->toArray());
  }
}