<?php
namespace Solcre\lmsuy\Service;

use \Solcre\lmsuy\Entity\SessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\Rakeback\SimpleRakeback;

class SessionService extends BaseService
{

    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
    }

    public function add($data, $strategies = null)
    {
        $session = new SessionEntity();
        $session->setDate(new \DateTime($data['date']));
        $session->setTitle($data['title']);
        $session->setDescription($data['description']);
        $session->setSeats($data['seats']);
        $session->setStartTime(new \DateTime($data['startTime']));
        $session->setStartTimeReal(new \DateTime($data['startTimeReal']));
        $session->setEndTime(new \DateTime($data['endTime']));
        $session->setRakebackClass($data['rakebackClass']);

        $this->entityManager->persist($session);
        $this->entityManager->flush($session);
    }

    public function update($data, $strategies = null)
    {

        $session = parent::fetch($data['idSession']);
        $session->setDate(new \DateTime($data['date']));
        $session->setTitle($data['title']);
        $session->setDescription($data['description']);
        $session->setSeats($data['seats']);
        $session->setStartTime(new \DateTime($data['startTime']));
        $session->setStartTimeReal(new \DateTime($data['startTimeReal']));
        $session->setEndTime(new \DateTime($data['endTime']));

        $this->entityManager->persist($session);
        $this->entityManager->flush($session);
    }

    public function delete($id, $entityObj = null)
    {
        $session = $this->entityManager->getReference('Solcre\lmsuy\Entity\SessionEntity', $id);

        $this->entityManager->remove($session);
        $this->entityManager->flush();
    }

    public function calculateRakeback($idSession)
    {
        $session = parent::fetch($idSession);

        $class = $session->getRakebackClass();

        $rakebackAlgorithm = new $class();
        ;
        foreach ($session->getSessionUsers() as $userSession) {
            $userSession->setAccumulatedPoints($rakebackAlgorithm->calculate($userSession));
            
            $this->entityManager->persist($userSession);   
        }

        $this->entityManager->flush(); 
    }    
}
