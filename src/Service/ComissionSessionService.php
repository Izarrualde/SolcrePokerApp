<?php
Namespace Solcre\lmsuy\Service;

Use \Solcre\lmsuy\Entity\ComissionSessionEntity;
Use Doctrine\ORM\EntityManager;

class ComissionSessionService extends BaseService {

	public function __construct(EntityManager $em)
	{
		parent::_construct($em);
	}

	public function add($data, $strategies = null)
	{
		/*$this->connection->insertComission($comission->getHour(), $comission->getComission(), $comission->getIdSession());*/
		$comission = new ComissionSessionEntity();
		$comission->setIdSession($data['idSession']);
		$comission->setHour($data['hour']);
		$comission->setComission($data['comission']);

		$this->EntityManager->persist($comission);
		$this->EntityManager->flush($comission);
	}

	public function update($data, $strategies = null)
	{
		$comission = parent::fetch($data['id']);
		$comission->setHour($data['hour']);
		$comission->setComission($data['comission']);

		$this->EntityManager->persist($comission);
		$this->EntityManager->flush($comission);
	}

	public function delete($id, entityObj = null)
	{	
		$comission = $this->entityManager->getReference('Solcre\lmsuy\Entity\ComissionSessionEntity', $id);
		$this->EntityManager->remove($comission);
		$this->EntityManager->flush();
	}

	public function findOne($id)
	{
		$comission = $this->connection->getDatosSessionComissionById($id);
		$comissionObject = new ComissionSession($comission->id, $comission->session_id, $comission->created_at, $comission->comission);
		return $comissionObject;
	}
/*
	public function find($idSession)
	{
		$datosComissions = $this->connection->getDatosSessionComissions($idSession);
		$comissions = array();//

		foreach ($datosComissions as $comission) 
		{
			$comissionObject = new ComissionSession($comission->id, $comission->session_id, $comission->created_at, $comission->comission);
			
			$comissions[] = $comissionObject; 
		}
		return $comissions;
	}
*/

}