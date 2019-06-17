<?php
Namespace Solcre\lmsuy\Service;

Use \Solcre\lmsuy\Entity\DealerTipSessionEntity;
Use Doctrine\ORM\EntityManager;

class DealerTipSessionService extends BaseService {

	public function __construct(EntityManager $em)
	{
		parent::__construct($em);
	}

	public function add($data, $strategies = null)
	{

		$data['hour'] = new \DateTime($data['hour']);

		$dealerTip = new DealerTipSessionEntity();
		$dealerTip->setHour($data['hour']);
		$dealerTip->setSession($this->entityManager->getReference('Solcre\lmsuy\Entity\SessionEntity', $data['idSession']));
		$dealerTip->setDealerTip($data['dealerTip']);

		$this->entityManager->persist($dealerTip);
		$this->entityManager->flush($dealerTip);
	}

	public function update($data, $strategies = null)
	{
		var_dump($data['hour']);
		$data['hour'] = new \DateTime($data['hour']);

		// $dealerTip = parent::fetch($data['id']);
		$dealerTip = $this->entityManager->getReference('Solcre\lmsuy\Entity\DealerTipSessionEntity', $data['id']);
		$dealerTip->setHour($data['hour']);
		$dealerTip->setDealerTip($data['dealerTip']);

		$this->entityManager->persist($dealerTip);
		$this->entityManager->flush($dealerTip);		
	}

	public function delete($id, $entityObj = null)
	{	
		$dealerTip = $this->entityManager->getReference('Solcre\lmsuy\Entity\DealerTipSessionEntity', $id);

		$this->entityManager->remove($dealerTip);
		$this->entityManager->flush();
	}
	
}