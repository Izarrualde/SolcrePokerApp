<?php
namespace Solcre\lmsuy\Service;

use \Solcre\lmsuy\Entity\ExpensesSessionEntity;
use Doctrine\ORM\EntityManager;
use \Solcre\lmsuy\Exception\ExpensesInvalidException;

class ExpensesSessionService extends BaseService
{

    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
    }

    public function add($data, $strategies = null)
    {
        if (!is_numeric($data['amount'])) {
            throw new ExpensesInvalidException();
        }

        $expenditure = new ExpensesSessionEntity();
        $session     = $this->entityManager->getReference('Solcre\lmsuy\Entity\SessionEntity', $data['idSession']);
        $expenditure->setSession($session);

        $expenditure->setDescription($data['description']);
        $expenditure->setAmount($data['amount']);

        $this->entityManager->persist($expenditure);
        $this->entityManager->flush($expenditure);
    }

    public function update($data, $strategies = null)
    {

        $expenditure = parent::fetch($data['id']);
        $expenditure->setDescription($data['description']);
        $expenditure->setAmount($data['amount']);

        $this->entityManager->persist($expenditure);
        $this->entityManager->flush($expenditure);
    }

    public function delete($id, $entityObj = null)
    {
        $expenditure = $this->entityManager->getReference('Solcre\lmsuy\Entity\ExpensesSessionEntity', $id);
        $this->entityManager->remove($expenditure);
        $this->entityManager->flush();
    }
}
