<?php
namespace Solcre\lmsuy\Rakeback;

use Solcre\lmsuy\Entity\UserSessionEntity;

class SimpleRakeback implements RakebackAlgorithm
{
  const RAKEBACK_PERCENTAGE = 0.01;

  public function calculate(UserSessionEntity $userSession)
  {
    return $userSession->getSession()->getComissionTotal()*SimpleRakeback::RAKEBACK_PERCENTAGE;
  }
}