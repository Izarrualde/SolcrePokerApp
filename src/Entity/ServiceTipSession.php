<?php
Namespace Solcre\PokerApp\Entity;

class ServiceTipSession 
{
	protected $id;
	protected $idSession;
	protected $hour;
	protected $serviceTip;

	public function __construct($id=null, $idSession=null, $hour="", $tip=null)
	{
		$this->setId($id);
		$this->setIdSession($idSession);
		$this->setHour($hour);
		$this->setServiceTip($tip);
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

	public function getServiceTip()
	{
		return $this->serviceTip;
	}

	public function setServiceTip($tip)
	{
		$this->serviceTip = $tip;
		return $this;
	}
}

?>