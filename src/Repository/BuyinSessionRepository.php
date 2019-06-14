<?php
namespace Solcre\lmsuy\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr\Join;
use Solcre\lmsuy\Entity\BuyinSessionEntity;
use Solcre\lmsuy\Entity\UserSessionEntity;
use Solcre\lmsuy\Repository\BaseRepository;

class BuyinSessionRepository extends BaseRepository {

	public function fetchAll($sessionId)
   {

       $qb = $this->_em->createQueryBuilder();
       $qb->select('b');
       $qb->from($this->_entityName, 'b');

       $qb->join(UserSessionEntity::class, 'u', Join::WITH, 'b.userSession = u.id');


       $qb->where('u.session=:param');

       $qb->setParameter('param', 15);
       
       //$qb->addOrderBy(‘c.order’, ‘ASC’);
       //$qb->addOrderBy(‘p.id’, ‘ASC’);
       return $qb->getQuery()->getResult();
    }
}

