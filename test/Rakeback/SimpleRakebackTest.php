<?php

use PHPUnit\Framework\TestCase;
use Solcre\lmsuy\Entity\SessionEntity;
use Solcre\lmsuy\Entity\UserSessionEntity;
use Solcre\lmsuy\Entity\ComissionSessionEntity;
use Solcre\lmsuy\Entity\ExpensesSessionEntity;
use Solcre\lmsuy\Service\SessionService;
use Solcre\lmsuy\Rakeback\SimpleRakeback;
use Doctrine\Common\Collections\ArrayCollection;

class SimpleRakebackTest extends TestCase
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
    
    $session->setSessionUsers($sessionUsers);
    $session->setSessionComissions($sessionComissions);
    $session->setRakebackClass('Solcre\lmsuy\Rakeback\SimpleRakeback');
    /*
    $sessionService = new SessionService()

    $simpleRakeback = new SimpleRakeback();
    $session->setRakebackAlgorithm($simpleRakeback);
    $session->calculatePoints();

    $this->assertEquals(1.1, $userSession->getAccumulatedPoints());
*/
  }
}