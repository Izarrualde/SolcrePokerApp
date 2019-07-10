<?php

use PHPUnit\Framework\TestCase;
use Solcre\lmsuy\Entity\SessionEntity;
use Solcre\lmsuy\Entity\UserSessionEntity;
use Solcre\lmsuy\Entity\ComissionSessionEntity;
use Solcre\lmsuy\Entity\ExpensesSessionEntity;
use Solcre\lmsuy\Rakeback\GrossEarningsRakeback;
use Doctrine\Common\Collections\ArrayCollection;

class GrossEarningsRakebackTest extends TestCase
{
  public function testCalculate()
  {

    $session = new SessionEntity(1);
    $comission1 = new ComissionSessionEntity();
    $comission1->setComission(50);
    $comission2 = new ComissionSessionEntity();
    $comission2->setComission(60);

    $sessionComissions = new ArrayCollection();
    $sessionComissions[] = $comission1;
    $sessionComissions[] = $comission2;

    $userSession = new UserSessionEntity();
    $userSession->setSession($session);

    $sessionUsers = new ArrayCollection();
    $sessionUsers[] = $userSession;
    
    $expenditure1 = new ExpensesSessionEntity();
    $expenditure1->setAmount(30);
    $expenditure2 = new ExpensesSessionEntity();
    $expenditure2->setAmount(30);

    $sessionExpenses = new ArrayCollection();
    $sessionExpenses[] = $expenditure1;
    $sessionExpenses[] = $expenditure2;

    $session->SetSessionExpenses($sessionExpenses);
    $session->setSessionUsers($sessionUsers);
    $session->setSessionComissions($sessionComissions);
    $session->setRakebackClass('Solcre\lmsuy\Rakeback\GrossEarningsRakeback');
/*
    $grossEarningsRakeback = new GrossEarningsRakeback();
    $session->setRakebackAlgorithm($grossEarningsRakeback);
    $session->calculatePoints();

    $this->assertEquals(0.5, $userSession->getAccumulatedPoints());
*/
  }
}