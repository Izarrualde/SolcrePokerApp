<?php
Namespace Solcre\lmsuy\Service;

Use \Solcre\lmsuy\Entity\ServiceTipSessionEntity;
Use Doctrine\ORM\EntityManager;

class ServiceTipSessionService extends BaseService {

	public function __construct(EntityManager $em)
	{
		parent::__construct($em);
	}

	public function add($data, $strategies = null)
	{

		$data['hour'] = new \DateTime($data['hour']);

		$serviceTip = new  ServiceTipSessionEntity();
		$serviceTip->setHour($data['hour']);
		$serviceTip->setSession($this->entityManager->getReference('Solcre\lmsuy\Entity\SessionEntity', $data['idSession']));
		$serviceTip->setServiceTip($data['serviceTip']);

		$this->entityManager->persist($serviceTip);
		$this->entityManager->flush($serviceTip);
	}

	public function update($data, $strategies = null)
	{
		$data['hour'] = new \DateTime($data['hour']);
		$serviceTip = $this->entityManager->getReference('Solcre\lmsuy\Entity\ServiceTipSessionEntity', $data['id']);
		// $serviceTip = parent::fetch($data['id']);
		$serviceTip->setHour($data['hour']);
		$serviceTip->setServiceTip($data['serviceTip']);

		$this->entityManager->persist($serviceTip);
		$this->entityManager->flush($serviceTip);
	}

	public function delete($id, $entityObj = null)
	{	
		$serviceTip = $this->entityManager->getReference('Solcre\lmsuy\Entity\ServiceTipSessionEntity', $id);

		$this->entityManager->remove($serviceTip);
		$this->entityManager->flush();
	}

	/*
	public function findOne($id)
	{
		$serviceTip = $this->connection->getDatosSessionServiceTipById($id);
		$serviceTipObject = new ServiceTipSession($serviceTip->id, $serviceTip->session_id, $serviceTip->created_at, $serviceTip->service_tip);
		return $serviceTipObject;
	}

	public function find($idSession)
	{
		$datosServiceTips = $this->connection->getDatosSessionServiceTips($idSession);
		$serviceTips = array();

		foreach ($datosServiceTips as $serviceTip) 
		{
			$serviceTipObject = new ServiceTipSession($serviceTip->id, $serviceTip->session_id, $serviceTip->created_at, $serviceTip->service_tip);
			
			$serviceTips[] = $serviceTipObject; 
		}
		return $serviceTips;
	}
	*/
}