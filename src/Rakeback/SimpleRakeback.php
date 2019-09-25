<?php
namespace Solcre\lmsuy\Rakeback;

use Solcre\Pokerclub\Entity\UserSessionEntity;
use Solcre\Pokerclub\Rakeback\RakebackAlgorithm;

class SimpleRakeback implements RakebackAlgorithm
{
    const RAKEBACK_PERCENTAGE = 0.015;

    public function calculate(UserSessionEntity $userSession)
    {
        return $userSession->getSession()->getComissionTotal()*SimpleRakeback::RAKEBACK_PERCENTAGE;
    }
}
