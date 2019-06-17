<?php
Namespace Solcre\lmsuy\Service;

Use \Solcre\lmsuy\Entity\BuyinSessionEntity;
Use Doctrine\ORM\EntityManager;

class BuyinSessionService extends BaseService {

	public function __construct(EntityManager $em)
	{
		parent::__construct($em);
	}

	public function fetchAllBuyins($sessionId)
    {
        return $this->repository->fetchAll($sessionId);
    }

	public function add($data, $strategies = null)
	{
		$data['hour'] = new \DateTime($data['hour']);

		$buyin = new BuyinSessionEntity();
		$buyin->setHour($data['hour']);
		$buyin->setAmountCash($data['amountCash']);
		$buyin->setAmountCredit($data['amountCredit']);
		$buyin->setCurrency(1);
		$userSession = $this->entityManager->getReference('Solcre\lmsuy\Entity\UserSessionEntity', $data['idUserSession']);


		$buyin->setUserSession($this->entityManager->getReference('Solcre\lmsuy\Entity\UserSessionEntity', $data['idUserSession']));
		$buyin->setSessionUserId($data['idUserSession']);
		$buyin->setIsApproved($data['approved']);

		$this->entityManager->persist($buyin);
		$this->entityManager->flush($buyin);
	}

	public function update($data, $strategies = null)
	{

		$data['hour'] = new \DateTime($data['hour']);
		$buyin = parent::fetch($data['id']);

		$buyin->setHour($data['hour']);
		$buyin->setAmountCash($data['amountCash']);
		$buyin->setAmountCredit($data['amountCredit']);

		$this->entityManager->persist($buyin);
		$this->entityManager->flush($buyin);
	}

	public function delete($id, $entityObj = null)
	{	
		$buyin = $this->entityManager->getReference('Solcre\lmsuy\Entity\BuyinSessionEntity', $id);

		$this->entityManager->remove($buyin);
		$this->entityManager->flush();
	}
}