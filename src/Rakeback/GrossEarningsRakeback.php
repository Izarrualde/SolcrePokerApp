<?php
namespace Solcre\lmsuy\Rakeback;

use Solcre\lmsuy\Entity\UserSessionEntity;

class GrossEarningsRakeback implements RakebackAlgorithm
{

  const RAKEBACK_PERCENTAGE = 0.01;

  public function calculate(UserSessionEntity $userSession)
  {
    return ($userSession->getSession()->getComissionTotal()
      - $userSession->getSession()->getExpensesTotal())
      * GrossEarningsRakeback::RAKEBACK_PERCENTAGE;
  }
}