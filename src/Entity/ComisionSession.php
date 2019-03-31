<?php
Namespace Solcre\PokerApp\Entity;

class ComissionSession
{
	protected $id;
	protected $idSession;
	protected $hour;
	protected $comission;

	public function __construct($id=null, $idSession=null, $hour=null, $comission=null)
	{
		$this->setId($id);
		$this->setIdSession($idSession);
		$this->setHour($hour);
		$this->setComission($comission);
	}

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	public function getIdSession()
	{
		return $this->idSession;
	}

	public function setIdSession($idSession)
	{
		$this->idSession = $idSession;
		return $this;
	}

	public function getHour()
	{
		return $this->hour;
	}

	public function setHour($hour)
	{
		$this->hour = $hour;
		return $this;
	}

	public function getComission()
	{
		return $this->comission;
	}

	public function setComission($comission)
	{
		$this->comission = $comission;
		return $this;
	}
}

?>