<?php
namespace Solcre\lmsuy\Service;

use Solcre\lmsuy\Entity\ComissionSessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\Exception\ComissionInvalidException;

class ComissionSessionService extends BaseService
{

    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
    }

    public function add($data, $strategies = null)
    {
        if (!is_numeric($data['comission'])) {
            throw new ComissionInvalidException();
        }
        $data['hour'] = new \DateTime($data['hour']);

        $comission    = new ComissionSessionEntity();
        $comission->setHour($data['hour']);
        $comission->setComission($data['comission']);
        $session = $this->entityManager->getReference('Solcre\lmsuy\Entity\SessionEntity', $data['idSession']);
        $comission->setSession($session);

        $this->entityManager->persist($comission);
        $this->entityManager->flush($comission);
    }

    public function update($data, $strategies = null)
    {
        if (!is_numeric($data['comission'])) {
            throw new ComissionInvalidException();
        }
        
        $data['hour'] = new \DateTime($data['hour']);
        $comission    = parent::fetch($data['id']);
        $comission->setHour($data['hour']);
        $comission->setComission($data['comission']);

        $this->entityManager->persist($comission);
        $this->entityManager->flush($comission);
    }

    public function delete($id, $entityObj = null)
    {
        $comission = $this->entityManager->getReference('Solcre\lmsuy\Entity\ComissionSessionEntity', $id);
        $this->entityManager->remove($comission);
        $this->entityManager->flush();
    }
}
