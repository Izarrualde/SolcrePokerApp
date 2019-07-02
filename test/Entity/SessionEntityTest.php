<?php

use PHPUnit\Framework\TestCase;
// use ReflectionMethod;
use Solcre\lmsuy\Entity\SessionEntity;
use Solcre\lmsuy\Entity\UserSessionEntity;
use Solcre\lmsuy\Entity\UserEntity;
use Solcre\lmsuy\Entity\DealerTipSessionEntity;
use Solcre\lmsuy\Entity\ServiceTipSessionEntity;
use Solcre\lmsuy\Entity\ExpensesSessionEntity;
use Solcre\lmsuy\Entity\ComissionSessionEntity;
use Solcre\lmsuy\Entity\BuyinSessionEntity;
use Doctrine\Common\Collections\ArrayCollection;

class SessionEntityTest extends TestCase
{

    public function testCreateWithParams()
    {
        $id = 1;
        $date = date_create('2019-06-26 15:00:00');
        $title = 'Mesa Mixta';
        $description = 'miercoles';
        $photo = null;
        $seats = 9;
        $startTime = date_create('2019-06-26 19:00:00');
        $startTimeReal = date_create('2019-06-26 19:30:00');
        $endTime = date_create('2019-06-26 23:00:00');

        $session = new SessionEntity(
            $id,
            $date,
            $title,
            $description,
            $photo,
            $seats,
            $startTime,
            $startTimeReal,
            $endTime
        );

        $this->assertEquals($id, $session->getId());
        $this->assertEquals($date, $session->getDate());
        $this->assertEquals($title, $session->getTitle());
        $this->assertEquals($description, $session->getDescription());
        $this->assertEquals($photo, $session->getPhoto());
        $this->assertEquals($seats, $session->getSeats());
        $this->assertEquals($startTime, $session->getStartTime());
        $this->assertEquals($startTimeReal, $session->getStartTimeReal());
        $this->assertEquals($endTime, $session->getEndTime());
    }

    public function testGetTotalCashout(){
        $session = new SessionEntity();
        $userSession1 = new userSessionEntity();
        $userSession1->setCashout(500);
        $userSession2 = new userSessionEntity();
        $userSession2->setCashout(600);

        $sessionUsers = new ArrayCollection();
        $sessionUsers[] = $userSession1;
        $sessionUsers[] = $userSession2;

        $session->setSessionUsers($sessionUsers);

        $this->assertEquals(1100, $session->getTotalCashout());
    }

    public function testGetDealerTipTotal()
    {
        $session = new SessionEntity();
        $dealerTip1 = new DealerTipSessionEntity();
        $dealerTip1->setDealerTip(50);
        $dealerTip2 = new DealerTipSessionEntity();
        $dealerTip2->setDealerTip(60);

        $sessionDealerTips = new ArrayCollection();
        $sessionDealerTips[] = $dealerTip1;
        $sessionDealerTips[] = $dealerTip2;

        $session->setSessionDealerTips($sessionDealerTips);

        $this->assertEquals(110, $session->getDealerTipTotal());
    }


    public function testGetServiceTipTotal(){
        $session = new SessionEntity();
        $serviceTip1 = new ServiceTipSessionEntity();
        $serviceTip1->setServiceTip(50);
        $serviceTip2 = new ServiceTipSessionEntity();
        $serviceTip2->setServiceTip(60);

        $sessionServiceTips = new ArrayCollection();
        $sessionServiceTips[] = $serviceTip1;
        $sessionServiceTips[] = $serviceTip2;

        $session->setSessionServiceTips($sessionServiceTips);

        $this->assertEquals(110, $session->getServiceTipTotal());
    }

    public function testGetExpensesTotal()
    {
        $session = new SessionEntity();
        $expenditure1 = new ExpensesSessionEntity();
        $expenditure1->setAmount(50);
        $expenditure2 = new ExpensesSessionEntity();
        $expenditure2->setAmount(60);

        $sessionExpenses = new ArrayCollection();
        $sessionExpenses[] = $expenditure1;
        $sessionExpenses[] = $expenditure2;

        $session->SetSessionExpenses($sessionExpenses);

        $this->assertEquals(110, $session->getExpensesTotal());
    }

    public function testGetComissionTotal()
    {
        $session = new SessionEntity();
        $comission1 = new ComissionSessionEntity();
        $comission1->setComission(50);
        $comission2 = new ComissionSessionEntity();
        $comission2->setComission(60);

        $sessionComissions = new ArrayCollection();
        $sessionComissions[] = $comission1;
        $sessionComissions[] = $comission2;

        $session->setSessionComissions($sessionComissions);

        $this->assertEquals(110, $session->getComissionTotal());
    }

    public function testGetBuyins()
    {
        $session = new SessionEntity();
        
        $userSession1 = new UserSessionEntity();
        $buyins1 = new ArrayCollection();
        $buyins1[] = new BuyinSessionEntity(1,100,0,$userSession1);
        $buyins1[] = new BuyinSessionEntity(2,200,0,$userSession1);
        $userSession1->setBuyins($buyins1);

        $userSession2 = new UserSessionEntity();
        $buyins2 = new ArrayCollection();
        $buyins2[] = new BuyinSessionEntity(3,300,0,$userSession2);
        $buyins2[] = new BuyinSessionEntity(4,400,0,$userSession2);
        $userSession2->setBuyins($buyins2);

        $userSessions = new ArrayCollection();
        $userSessions[] = $userSession1;
        $userSessions[] = $userSession2;
        $session->setSessionUsers($userSessions);

        $expectedBuyins = array_merge($buyins1->toArray(), $buyins2->toArray());

        $i = 0;
        foreach ($session->getBuyins() as $buyin) {
            $this->assertSame($buyin, $expectedBuyins[$i]);
            $i++;
        }
    }

    public function testGetTotalPlayed() 
    {
        $session = new SessionEntity();
        
        $userSession1 = new UserSessionEntity();
        $buyins1 = new ArrayCollection();
        $buyins1[] = new BuyinSessionEntity(1,100,0,$userSession1);
        $buyins1[] = new BuyinSessionEntity(2,200,0,$userSession1);
        $userSession1->setBuyins($buyins1);

        $userSession2 = new UserSessionEntity();
        $buyins2 = new ArrayCollection();
        $buyins2[] = new BuyinSessionEntity(3,300,0,$userSession2);
        $buyins2[] = new BuyinSessionEntity(4,400,0,$userSession2);
        $userSession2->setBuyins($buyins2);

        $userSessions = new ArrayCollection();
        $userSessions[] = $userSession1;
        $userSessions[] = $userSession2;
        $session->setSessionUsers($userSessions);

        $expectedBuyins = 100 + 200 + 300 + 400;

        $this->assertEquals($expectedBuyins, $session->getTotalPlayed()); 
    }

    public function testValidateSessionExpectedTrue()
    {
        $session = new SessionEntity();

        $comission1 = new ComissionSessionEntity();
        $comission1->setComission(50);
        $comission2 = new ComissionSessionEntity();
        $comission2->setComission(60);
        $sessionComissions = new ArrayCollection();
        $sessionComissions[] = $comission1;
        $sessionComissions[] = $comission2;

        $session->setSessionComissions($sessionComissions);

        $userSession1 = new userSessionEntity();
        $buyin1 = new BuyinSessionEntity(1, 1000, 0, $userSession1);
        $buyins1 = New ArrayCollection();
        $buyins1[] = $buyin1;
        $userSession2 = new userSessionEntity();
        $buyin2 = new BuyinSessionEntity(2, 430, 0, $userSession2);
        $buyins2 = new ArrayCollection();
        $buyins2[] = $buyin2;
        
        $userSession1->setBuyins($buyins1);
        $userSession1->setCashout(500);
        $userSession2->setBuyins($buyins2);
        $userSession2->setCashout(600);
        $sessionUsers = new ArrayCollection();
        $sessionUsers[] = $userSession1;
        $sessionUsers[] = $userSession2;

        $session->setSessionUsers($sessionUsers);

        $dealerTip1 = new DealerTipSessionEntity();
        $dealerTip1->setDealerTip(50);
        $dealerTip2 = new DealerTipSessionEntity();
        $dealerTip2->setDealerTip(60);
        $sessionDealerTips = new ArrayCollection();
        $sessionDealerTips[] = $dealerTip1;
        $sessionDealerTips[] = $dealerTip2;

        $session->setSessionDealerTips($sessionDealerTips);

        $serviceTip1 = new ServiceTipSessionEntity();
        $serviceTip1->setServiceTip(50);
        $serviceTip2 = new ServiceTipSessionEntity();
        $serviceTip2->setServiceTip(60);
        $sessionServiceTips = new ArrayCollection();
        $sessionServiceTips[] = $serviceTip1;
        $sessionServiceTips[] = $serviceTip2;

        $session->setSessionServiceTips($sessionServiceTips);
        /*
        $total = $session->getTotalCashout() +
        $session->getComissionTotal() +
        $session->getDealerTipTotal()+
        $session-> getServiceTipTotal();*/

        $this->assertTrue($session->validateSession($session));
    }

    public function testValidateSessionExpectedFalse()
    {
        $session = new SessionEntity();

        $comission1 = new ComissionSessionEntity();
        $comission1->setComission(50);
        $comission2 = new ComissionSessionEntity();
        $comission2->setComission(60);
        $sessionComissions = new ArrayCollection();
        $sessionComissions[] = $comission1;
        $sessionComissions[] = $comission2;

        $session->setSessionComissions($sessionComissions);

        $userSession1 = new userSessionEntity();
        $buyin1 = new BuyinSessionEntity(1, 1000, 0, $userSession1);
        $buyins1 = New ArrayCollection();
        $buyins1[] = $buyin1;
        $userSession2 = new userSessionEntity();
        $buyin2 = new BuyinSessionEntity(2, 430, 0, $userSession2);
        $buyins2 = new ArrayCollection();
        $buyins2[] = $buyin2;
        
        $userSession1->setBuyins($buyins1);
        $userSession1->setCashout(1600);
        $userSession2->setBuyins($buyins2);
        $userSession2->setCashout(600);
        $sessionUsers = new ArrayCollection();
        $sessionUsers[] = $userSession1;
        $sessionUsers[] = $userSession2;

        $session->setSessionUsers($sessionUsers);

        $dealerTip1 = new DealerTipSessionEntity();
        $dealerTip1->setDealerTip(50);
        $dealerTip2 = new DealerTipSessionEntity();
        $dealerTip2->setDealerTip(60);
        $sessionDealerTips = new ArrayCollection();
        $sessionDealerTips[] = $dealerTip1;
        $sessionDealerTips[] = $dealerTip2;

        $session->setSessionDealerTips($sessionDealerTips);

        $serviceTip1 = new ServiceTipSessionEntity();
        $serviceTip1->setServiceTip(50);
        $serviceTip2 = new ServiceTipSessionEntity();
        $serviceTip2->setServiceTip(60);
        $sessionServiceTips = new ArrayCollection();
        $sessionServiceTips[] = $serviceTip1;
        $sessionServiceTips[] = $serviceTip2;

        $session->setSessionServiceTips($sessionServiceTips);

        $this->assertFalse($session->validateSession($session));
    }

    public function testGetActivePlayersWithoutActives()
    {
        $session = new SessionEntity();
        $userSession1 = new userSessionEntity();
        $userSession2 = new userSessionEntity();
        $userSession3 = new userSessionEntity();

        $user1 = new UserEntity(1);
        $user2 = new UserEntity(2);
        $user3 = new UserEntity(3);


        $userSession1->setUser($user1);


        $userSession1->setStart(date_create('2019-06-26 19:00:00'));
        $userSession1->setEnd(date_create('2019-06-26 22:00:00'));

        $userSession2->setUser($user2);
        $userSession2->setStart(date_create('2019-06-26 19:00:00'));
        $userSession2->setEnd(date_create('2019-06-26 23:00:00'));

        $userSession3->setUser($user3);


        $sessionUsers = new ArrayCollection();
        $sessionUsers[] = $userSession1;
        $sessionUsers[] = $userSession2;
        $sessionUsers[] = $userSession3;


        $session->setSessionUsers($sessionUsers);

        $this->assertEquals(0, count($session->getActivePlayers()));
    }
 
    public function testGetActivePlayersWithActives()
    {
        $session = new SessionEntity();
        $userSession1 = new userSessionEntity();
        $userSession2 = new userSessionEntity();
        $userSession3 = new userSessionEntity();

        $user1 = new UserEntity(1);
        $user2 = new UserEntity(2);
        $user3 = new UserEntity(3);

        $userSession1->setUser($user1);
        $userSession1->setStart(date_create('2019-06-26 19:00:00'));

        $userSession2->setUser($user2);
        $userSession2->setStart(date_create('2019-06-26 19:00:00'));

        $userSession3->setUser($user3);
        


        $sessionUsers = new ArrayCollection();
        $sessionUsers[] = $userSession1;
        $sessionUsers[] = $userSession2;
        $sessionUsers[] = $userSession3;


        $expectedActivePlayers[] = $userSession1->getUser()->getId();
        $expectedActivePlayers[] = $userSession2->getUser()->getId();

        $session->setSessionUsers($sessionUsers);

        $this->assertEquals(2, count($session->getActivePlayers()));
        
        $this->assertEquals($expectedActivePlayers, $session->getActivePlayers());
    }

    public function testGetDistinctPlayersWithoutEquals()
    {
        $session = new SessionEntity();
        $userSession1 = new userSessionEntity();
        $userSession2 = new userSessionEntity();
        $userSession3 = new userSessionEntity();

        $user1 = new UserEntity(1);
        $user2 = new UserEntity(2);
        $user3 = new UserEntity(3);

        $userSession1->setUser($user1);
        $userSession1->setStart(date_create('2019-06-26 19:00:00'));

        $userSession2->setUser($user2);
        $userSession2->setStart(date_create('2019-06-26 19:00:00'));

        $userSession3->setUser($user3);

        $sessionUsers = new ArrayCollection();
        $sessionUsers[] = $userSession1;
        $sessionUsers[] = $userSession2;
        $sessionUsers[] = $userSession3;

        $expectedDistinctPlayers[] = $userSession1->getUser()->getId();
        $expectedDistinctPlayers[] = $userSession2->getUser()->getId();
        $expectedDistinctPlayers[] = $userSession3->getUser()->getId();

        $session->setSessionUsers($sessionUsers);


        $this->assertEquals(3, count($session->getDistinctPlayers()));

        $this->assertEquals($expectedDistinctPlayers, $session->getDistinctPlayers());
    }

    public function testGetDistinctPlayersWithEquals()
    {
        $session = new SessionEntity();
        $userSession1 = new userSessionEntity();
        $userSession2 = new userSessionEntity();
        $userSession3 = new userSessionEntity();

        $user1 = new UserEntity(1);
        $user2 = new UserEntity(2);
        $user3 = new UserEntity(2);

        $userSession1->setUser($user1);
        $userSession1->setStart(date_create('2019-06-26 19:00:00'));

        $userSession2->setUser($user2);
        $userSession2->setStart(date_create('2019-06-26 19:00:00'));

        $userSession3->setUser($user3);

        $sessionUsers = new ArrayCollection();
        $sessionUsers[] = $userSession1;
        $sessionUsers[] = $userSession2;
        $sessionUsers[] = $userSession3;

        $expectedDistinctPlayers[] = $userSession1->getUser()->getId();
        $expectedDistinctPlayers[] = $userSession2->getUser()->getId();

        $session->setSessionUsers($sessionUsers);


        $this->assertEquals(2, count($session->getDistinctPlayers()));

        $this->assertEquals($expectedDistinctPlayers, $session->getDistinctPlayers());
    }

    public function testToArray()
    {
        $session = new SessionEntity();
        $userSession1 = new userSessionEntity();
        $userSession2 = new userSessionEntity();
        $userSession3 = new userSessionEntity();

        $user1 = new UserEntity(1);
        $user2 = new UserEntity(2);
        $user3 = new UserEntity(3);

        $userSession1->setUser($user1);
        $userSession1->setStart(date_create('2019-06-26 19:00:00'));

        $userSession2->setUser($user2);
        $userSession2->setStart(date_create('2019-06-26 19:00:00'));

        $userSession3->setUser($user3);

        $sessionUsers = new ArrayCollection();
        $sessionUsers[] = $userSession1;
        $sessionUsers[] = $userSession2;
        $sessionUsers[] = $userSession3;

        $expectedDistinctPlayers[] = $userSession1->getUser()->getId();
        $expectedDistinctPlayers[] = $userSession2->getUser()->getId();
        $expectedDistinctPlayers[] = $userSession3->getUser()->getId();

        $expectedActivePlayers[] = $userSession1->getUser()->getId();
        $expectedActivePlayers[] = $userSession2->getUser()->getId();

        $comission1 = new ComissionSessionEntity();
        $comission1->setComission(50);
        $comission2 = new ComissionSessionEntity();
        $comission2->setComission(50);

        $sessionComissions = new ArrayCollection();
        $sessionComissions[] = $comission1;
        $sessionComissions[] = $comission2;

        $session->setSessionComissions($sessionComissions);

        $session->setId(1);
        $session->setDate(date_create('2019-06-26 18:00:00'));
        $session->setTitle('Mesa Mixta');
        $session->setDescription('miercoles');
        $session->setStartTime(date_create('2019-06-26 19:00:00'));
        $session->setStartTimeReal(date_create('2019-06-26 19:00:00'));
        $session->setSeats(9);
        $session->setStartTime(date_create('2019-06-26 19:00:00'));
        $session->setStartTimeReal(date_create('2019-06-26 19:00:00'));
        $session->setEndTime(date_create('2019-06-26 23:00:00'));


        $session->setSessionUsers($sessionUsers); 

        $expenditure1 = new ExpensesSessionEntity();
        $expenditure1->setAmount(50);
        $expenditure2 = new ExpensesSessionEntity();
        $expenditure2->setAmount(60);

        $sessionExpenses = new ArrayCollection();
        $sessionExpenses[] = $expenditure1;
        $sessionExpenses[] = $expenditure2;

        $session->SetSessionExpenses($sessionExpenses);

        $dealerTip1 = new DealerTipSessionEntity();
        $dealerTip1->setDealerTip(50);
        $dealerTip2 = new DealerTipSessionEntity();
        $dealerTip2->setDealerTip(70);

        $sessionDealerTips = new ArrayCollection();
        $sessionDealerTips[] = $dealerTip1;
        $sessionDealerTips[] = $dealerTip2;

        $session->setSessionDealerTips($sessionDealerTips);
        $serviceTip1 = new ServiceTipSessionEntity();
        $serviceTip1->setServiceTip(50);
        $serviceTip2 = new ServiceTipSessionEntity();
        $serviceTip2->setServiceTip(80);

        $sessionServiceTips = new ArrayCollection();
        $sessionServiceTips[] = $serviceTip1;
        $sessionServiceTips[] = $serviceTip2;

        $session->setSessionServiceTips($sessionServiceTips);

        $expectedArray = [
            'id'                 => 1,
            'created_at'         => date_create('2019-06-26 18:00:00'),
            'title'              => 'Mesa Mixta',
            'description'        => 'miercoles',
            'startTime'          => date_create('2019-06-26 19:00:00'),
            'startTimeReal'      => date_create('2019-06-26 19:00:00'),
            'countActivePlayers' => 2,
            'activePlayers'      => $expectedActivePlayers,
            'distinctPlayers'    => $expectedDistinctPlayers,
            'seats'              => 9,
            'endTime'            => date_create('2019-06-26 23:00:00'),
            'comissionTotal'     => 100,
            'expensesTotal'      => 110,
            'dealerTipTotal'     => 120,
            'serviceTipTotal'    => 130
        ];

        $sessionArray = $session->toArray();

        $this->assertEquals($expectedArray, $session->toArray());
        /*
        foreach ($expectedArray as $key => $value) {
            $this->assertEquals($expectedArray[$key], $sessionArray[$key]);
        };
        */

        // $this->assertEquals($expectedArray, $sessionArray)


    }
      /*  return  [
        'id'                 => $this->getId(),
        'created_at'         => $this->getDate(),
        'title'              => $this->getTitle(),
        'description'        => $this->getDescription(),
        'startTime'          => $this->getStartTime(),
        'startTimeReal'      => $this->getStartTimeReal(),
        'countActivePlayers' => count($this->getActivePlayers()),
        'activePlayers'      => $this->getActivePlayers(),
        'distinctPlayers'    => $this->getDistinctPlayers(),
        'seats'              => $this->getSeats(),
        'endTime'            => $this->getEndTime(),
        'comissionTotal'     => $this->getComissionTotal(),
        'expensesTotal'      => $this->getExpensesTotal(),
        'dealerTipTotal'     => $this->getDealerTipTotal(),
        'serviceTipTotal'    => $this->getServiceTipTotal()
        ];
        */
}

