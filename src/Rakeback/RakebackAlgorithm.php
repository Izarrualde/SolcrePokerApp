<?php 
namespace Solcre\lmsuy\Rakeback;

use Solcre\lmsuy\Entity\UserSessionEntity;

interface RakeBackAlgorithm
{
  public function calculate(UserSessionEntity $userSession);
}

