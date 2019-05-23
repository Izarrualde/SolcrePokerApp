<?php
Namespace Solcre\lmsuy\Service;

Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\ComissionSession;

class ComissionSessionService {

	protected $connection;

	public function __construct(ConnectLmsuy_db $connection)
	{
		$this->connection = $connection;
	}

	public function add(ComissionSession $comission)
	{
		$this->connection->insertComission($comission->getHour(), $comission->getComission(), $comission->getIdSession());
	}

	public function update(ComissionSession $comission)
	{

		$this->connection-> updateComission($comission->getIdSession(), $comission->getHour(), $comission->getComission(), $comission->getId());
	}

	public function delete(ComissionSession $comission)
	{	
		$this->connection->deleteComission($comission->getId());
	}

	public function findOne($id)
	{
		$datosComission = $this->connection->getDatosSessionComissionById($id);
		$comissionObject = new ComissionSession($comission->id, $comission->session_id, $comission->created_at, $comission->comission);
		return $comissionObject;
	}

	public function find($IdSession)
	{
		$datosComissions = $this->connection->getDatosSessionComissions($IdSession);
		$users = array();//

		foreach ($datosComissions as $comission) 
		{
			$comissionObject = new ComissionSession($comission->id, $comission->session_id, $comission->created_at, $comission->comission);
			
			$comissions[] = $comissionObject; 
		}
		return $comissions;
	}

}