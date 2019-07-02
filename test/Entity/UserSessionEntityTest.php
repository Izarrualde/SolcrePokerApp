<?php

use PHPUnit\Framework\TestCase;
// use ReflectionMethod;
use Solcre\lmsuy\Entity\UserSessionEntity;
use Solcre\lmsuy\Entity\BuyinSessionEntity;
use Solcre\lmsuy\Entity\SessionEntity;
use Solcre\lmsuy\Entity\UserEntity;
use Doctrine\Common\Collections\ArrayCollection;

class UserSessionEntityTest extends TestCase
{

  public function testCreate()
  {
    $userSession = new UserSessionEntity();

    $this->assertInstanceOf(ArrayCollection::class, $userSession->getBuyins());
  }

 public function testCreateWithParams()
  {
    $id = 10;
    $session = new SessionEntity(3);
    $idUser = 11;
    $isApproved = true;
    $accumulatedPoints = 100;
    $cashout = 500;
    $start = date_create('2019-06-26 19:00:00');
    $end = date_create('2019-06-26 23:00:00');
    $user = New UserEntity(5);

    $userSession = new UserSessionEntity(
      $id,
      $session,
      $idUser,
      $isApproved,
      $accumulatedPoints,
      $cashout,
      $start,
      $end,
      $user
    );

    $this->assertEquals($id, $userSession->getId());
    $this->assertTrue($userSession->getIsApproved());
    $this->assertEquals($accumulatedPoints, $userSession->getAccumulatedPoints());
    $this->assertEquals($cashout, $userSession->getCashout());
    $this->assertEquals($start, $userSession->getStart());
    $this->assertEquals($end, $userSession->getEnd());
    $this->assertSame($session, $userSession->getSession());
    $this->assertSame($user, $userSession->getUser());
    $this->assertEquals(3, $userSession->getSession()->getId());
    $this->assertEquals(5, $userSession->getUser()->getId());
  }

  public function testGetDurationWithMoreThanOneDay() {

    $userSession = new UserSessionEntity();

    $userSession->setStart(date_create('2019-06-25 19:00:00'));
    $userSession->setEnd(date_create('2019-06-27 20:00:00'));

    $this->assertEquals(49, $userSession->getDuration());
  } 

  public function testGetDurationWithDecimals() {

    $userSession = new UserSessionEntity();

    $userSession->setStart(date_create('2019-06-26 19:00:00'));
    $userSession->setEnd(date_create('2019-06-26 20:15:00'));

    $this->assertEquals(1.25, $userSession->getDuration());
  }

  public function testGetDurationRounding() {


    $userSession = new UserSessionEntity();

    $userSession->setStart(date_create('2019-06-26 19:00:00'));
    $userSession->setEnd(date_create('2019-06-26 20:29:59'));

    $this->assertEquals(1.25, $userSession->getDuration());
  }

  public function testGetCashin(){

    $userSession = new UserSessionEntity();

    $buyin = new BuyinSessionEntity(1, null, null, $userSession);
    $buyin->setAmountCash(500);
    $buyin->setAmountCredit(100);

    $buyin2 = new BuyinSessionEntity(2, null, null, $userSession);
    $buyin2->setAmountCash(15);
    $buyin2->setAmountCredit(35);

    $buyins = New ArrayCollection();
    $buyins[] = $buyin;
    $buyins[] = $buyin2;

    $userSession->setBuyins($buyins);

    $this->assertEquals(650, $userSession->getCashin());
  }

  public function testGetCashinWithoutBuyin()
  {
    $userSession = new UserSessionEntity();

    $this->assertEquals(0, $userSession->getCashin());
  }
  public function testGetResult()
  {
    $userSession = new UserSessionEntity();

    $buyin = new BuyinSessionEntity(1, null, null, $userSession);
    $buyin->setAmountCash(500);
    $buyin->setAmountCredit(100);

    $buyin2 = new BuyinSessionEntity(2, null, null, $userSession);
    $buyin2->setAmountCash(15);
    $buyin2->setAmountCredit(35);

    $buyins = New ArrayCollection();
    $buyins[] = $buyin;
    $buyins[] = $buyin2;

    $userSession->setBuyins($buyins);
    $userSession->setCashout(1000);

    $this->assertEquals(350, $userSession->getResult());
  }

  public function testGetResultWithoutCashout()
  {
    $userSession = new UserSessionEntity();

    $buyin = new BuyinSessionEntity(1, null, null, $userSession);
    $buyin->setAmountCash(500);
    $buyin->setAmountCredit(100);

    $buyin2 = new BuyinSessionEntity(2, null, null, $userSession);
    $buyin2->setAmountCash(15);
    $buyin2->setAmountCredit(35);

    $buyins = New ArrayCollection();
    $buyins[] = $buyin;
    $buyins[] = $buyin2;

    $userSession->setBuyins($buyins);

    $this->assertEquals(-650, $userSession->getResult());
  }

  public function testGetTotalCreditWithoutBuyins()
  {
    $userSession = new UserSessionEntity();

    $this->assertEquals(0, $userSession->getTotalCredit());
  }

  public function testGetTotalCreditWithBuyins()
  {
    $userSession = new UserSessionEntity();

    $buyin = new BuyinSessionEntity(1, null, null, $userSession);
    $buyin->setAmountCash(500);
    $buyin->setAmountCredit(100);

    $buyin2 = new BuyinSessionEntity(2, null, null, $userSession);
    $buyin2->setAmountCash(15);
    $buyin2->setAmountCredit(35);

    $buyins = New ArrayCollection();
    $buyins[] = $buyin;
    $buyins[] = $buyin2;

    $userSession->setBuyins($buyins);

    $this->assertEquals(135, $userSession->getTotalCredit());
  }

  public function testToArray()
  {
    $user = new UserEntity(3);
    $user->setName('Diego');
    $user->setLastname('Rod');

    $session = new SessionEntity(2);

    $userSession = new UserSessionEntity();
    $userSession->setUser($user);
    $userSession->setSession($session);
    $userSession->setId(1);
    $userSession->setIdUser($user->getId());
    $userSession->setIsApproved(1);
    $userSession->setCashout(0);
    $userSession->setStart(date_create('2019-06-26 19:00:00'));
    $userSession->setEnd(date_create('2019-06-26 23:00:00'));

    $buyin1 = new BuyinSessionEntity(1, 1000, 200, $userSession);
    $buyins1 = New ArrayCollection();
    $buyins1[] = $buyin1;
    $userSession->setBuyins($buyins1);

    $expectedArray = [
      'id'          => 1,
      'idSession'   => 2,
      'idUser'      => 3,
      'isApproved'  => 1,
      'cashout'     => 0,
      'startTime'   => date_create('2019-06-26 19:00:00'),
      'endTime'     => date_create('2019-06-26 23:00:00'),
      'cashin'      => 1200,
      'totalCredit' => 200,
      'user'        => $user->toArray(),
      'session'     =>$session->toArray()
    ];

    $this->assertEquals($expectedArray, $userSession->toArray());

  }




}