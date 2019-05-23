<?php
Namespace Solcre\lmsuy\Service;

Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\SessionEntity;

class SessionService {

	protected $connection;

	public function __construct(ConnectLmsuy_db $connection)
	{
		$this->connection = $connection;
	}

	public function add(SessionEntity $session)
	{
		$this->connection->insertSession($session->getDate(), $session->geTitle(), $session->getDescription(), $session->getSeats(), $session->getStartTime(), $session->getStartTimeReal(), $session->getEndTime());
	}

	public function update(SessionEntity $session)
	{
		$this->connection->updateSession($session->getDate(), $session->getTitle(), $session->getDescription(), $session->getSeats(), $session->getStartTimeReal(), $session->getEndTime(), $session->getId());
	}

	public function delete(SessionEntity $session)
	{	
		$this->connection->deleteSession($session->getId());
	}

	public function findOne($id)
	{
		$datosSession = $this->connection->getDatosSessionById($id);
		$sessionObject = new SessionEntity($datosSession->id, $datosSession->created_at, $datosSession->title, $datosSession->description, null /*photo*/, $datosSession->count_of_seats, null /*seatswaiting*/ , null /*reservewainting*/, $datosSession->start_at, $datosSession->real_start_at, $datosSession->end_at);
		return $sessionObject;
	}

	public function find()
	{
		$datosSessions = $this->connection->getDatosSessions();
		$sessions = array();//

		foreach ($datosSessions as $session) 
		{
			$sessionObject = new SessionEntity($session->id, $session->created_at, $session->title, $session->description, null /*photo*/, $session->count_of_seats, null /*seatswaiting*/ , null /*reservewainting*/, $session->start_at, $session->real_start_at, $session->end_at);
			
			$sessions[] = $sessionObject; 
		}
		return $sessions;
	}
}

