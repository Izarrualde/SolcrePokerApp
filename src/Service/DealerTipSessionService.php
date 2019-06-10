<?php
Namespace Solcre\lmsuy\Service;

Use \Solcre\lmsuy\Entity\DealerTipSessionEntity;
Use Doctrine\ORM\EntityManager;

class DealerTipSessionService extends BaseService {

	public function __construct(EntityManager $em)
	{
		parent::__construct($em);
	}

	public function add(DealerTipSession $dealerTip)
	{
		$dealerTip = new DealerTipSession();
		$dealerTip->setHour($data['hour']);
		$dealerTip->setIdSession($data['idSession']);
		$dealerTip->setDealerTip($data['dealerTip']);

		$this->EntityManager->persist($dealerTip);
		$this->EntityManager->flush($dealerTip);
	}

	public function update($data, $strategies = null)
	{
		$dealerTip = parent::fetch($data['id']);
		$dealerTip->setHour($data['hour']);
		$dealerTip->setDealerTip($data['dealerTip']);

		$this->EntityManager->persist($dealerTip);
		$this->EntityManager->flush($dealerTip);		
	}

	public function delete($id, $entityObj = null)
	{	
		$dealerTip = $this->entityManager->getReference('Solcre\lmsuy\Entity\DealerTipSessionEntity', $id);

		$this->entityManager->remove($dealerTip);
		$this->entityManager->flush();
	}

	/*
	public function findOne($id)
	{
		$dealerTip = $this->connection->getDatosSessionDealerTipById($id);
		$dealerTipObject = new DealerTipSession($dealerTip->id, $dealerTip->session_id, $dealerTip->created_at, $dealerTip->dealer_tip);
		return $dealerTipObject;
	}

	public function find($idSession)
	{
		$datosDealerTips = $this->connection->getDatosSessionDealerTips($idSession);
		$dealerTips = array();//

		foreach ($datosDealerTips as $dealerTip) 
		{
			$dealerTipObject = new DealerTipSession($dealerTip->id, $dealerTip->session_id, $dealerTip->created_at, $dealerTip->dealer_tip);
			
			$dealerTips[] = $dealerTipObject; 
		}
		return $dealerTips;
	}
	*/
	
}