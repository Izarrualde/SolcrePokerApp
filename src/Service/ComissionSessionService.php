<?php
Namespace Solcre\lmsuy\Service;

Use \Solcre\lmsuy\Entity\ComissionSessionEntity;
Use Doctrine\ORM\EntityManager;

class ComissionSessionService extends BaseService {

	public function __construct(EntityManager $em)
	{
		parent::__construct($em);
	}

	public function add($data, $strategies = null)
	{

		$data['hour'] = new \DateTime($data['hour']);

		$comission = new ComissionSessionEntity();
		$comission->setSession($this->entityManager->getReference('Solcre\lmsuy\Entity\SessionEntity', $data['idSession']));

		$comission->setHour($data['hour']);
		$comission->setComission($data['comission']);

		$this->entityManager->persist($comission);
		$this->entityManager->flush($comission);
	}

	public function update($data, $strategies = null)
	{
		
		$data['hour'] = new \DateTime($data['hour']);

		$comission = parent::fetch($data['id']);
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