<?php
Namespace Solcre\lmsuy\Entity;

class DealerTipSession 
{
	protected $id;
	protected $idSession;
	protected $hour;
	protected $dealerTip;

	public function __construct($id=null, $idSession=null, $hour="", $tip=null)
	{
		$this->setId($id);
		$this->setIdSession($idSession);
		$this->setHour($hour);
		$this->setDealerTip($tip);
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

	public function getDealerTip()
	{
		return $this->dealerTip;
	}

	public function setDealerTip($tip)
	{
		$this->dealerTip = $tip;
		return $this;
	}
	public function toArray(){
		return  [
			'id' => $this->getId(),
			'idSession' => $this->getIdSession(),
			'hour' => $this->getHour(),
			'dealerTip' => $this->getDealerTip()
		];
	}

}

?>