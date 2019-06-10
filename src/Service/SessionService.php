<?php
Namespace Solcre\lmsuy\Service;

Use \Solcre\lmsuy\Entity\SessionEntity;
Use Doctrine\ORM\EntityManager;

class SessionService extends BaseService {

	public function __construct(EntityManager $em)
	{
		parent::__construct($em);
	}

	public function add($data, $strategies = null)
	{
		// $this->connection->insertSession($session->getDate(), $session->getTitle(), $session->getDescription(), $session->getSeats(), $session->getStartTime(), $session->getStartTimeReal(), $session->getEndTime());

		$session = new SessionEntity();
		$session->setDate($data['date']);
		$session->setTitle($data['title']);
		$session->setDescription($data['description']);
		$session->setSeats($data['seats']);
		$session->setStartTime($data['startTime']);
		$session->setStartTimeReal($data['startTimeReal']);
		$session->setEndTime($data['endTime']);

		$this->EntityManager->persist($session);
		$this->EntityManager->flush($session);
	}

	public function update($data, $strategies = null)
	{
		$session = parent::fetch($data['id']);
		$session->setDate($data['date']);
		$session->setTitle($data['title']);
		$session->setDescription($data['description']);
		$session->setSeats($data['seats']);
		$session->setStartTime($data['startTime']);
		$session->setStartTimeReal($data['startTimeReal']);
		$session->setEndTime($data['endTime']);

		$this->EntityManager->persist($session);
		$this->EntityManager->flush($session);
	}

	public function delete($id, $entityObj = null)
	{	
		$session = $this->entityManager->getReference('Solcre\lmsuy\Entity\SessionEntity', $id);

		$this->entityManager->remove($session);
		$this->entityManager->flush();
	}

	/*
	public function findOne($id)
	{
		$datosSession = $this->connection->getDatosSessionById($id);
		
		$sessionObject = new SessionEntity($datosSession->id, $datosSession->created_at, $datosSession->title, $datosSession->description, null /*photo*//*, $datosSession->count_of_seats, null /*seatswaiting*//* , null /*reservewainting*//*, $datosSession->start_at, $datosSession->real_start_at, $datosSession->end_at);
		return $sessionObject;
	}

	public function find()
	{
		$datosSessions = $this->connection->getDatosSessions();
		$sessions = array();//

		foreach ($datosSessions as $session) 
		{
			$sessionObject = new SessionEntity($session->id, $session->created_at, $session->title, $session->description, null /*photo*//*, $session->count_of_seats, null /*seatswaiting*//* , null /*reservewainting*//*, $session->start_at, $session->real_start_at, $session->end_at);
			
			$sessions[] = $sessionObject; 
		}
		return $sessions;
	}
	*/
}