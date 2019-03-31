<?php
Namespace Solcre\PokerApp\Entity;

class BuyinSession 
{
	protected $id;
	protected $idSession;
	protected $idPlayer;
	protected $amountCash;
	protected $amountCredit;
	protected $currency;
	protected $hour;
	protected $approved;

	public function __construct($id=null, $idSession=null, $idPlayer=null, $amountCash=null, $amountCredito=null, $currency=null, $hour=null, $approved=null)
	{
		$this->setId($id);
		$this->setIdSession($idSession);
		$this->setIdPlayer($idPlayer);
		$this->setamountCash($amountCash);
		$this->setamountCredito($amountCredito);
		$this->setCurrency($currency);
		$this->setHour($hour);
		$this->setApproved($approved);
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

	public function getIdPlayer() 
	{
		return $this->idPlayer;
	}

	public function setIdPlayer($idPlayer)
	{
		$this->idPlayer = $idPlayer;
		return $this;
	}

	public function getAmountCash()
	{
		return $this->amountCash;
	}

	public function setAmountCash($amountCash)
	{
		$this->amountCash = $amountCash;
		return $this;
	}

	public function getamountCredit()
	{
		return $this->amountCredit;
	}

	public function setAmountCredito($amountCredit)
	{
		$this->amountCredit = $amountCredit;
		return $this;
	}

	public function getCurrency()
	{
		return $this->currency;
	}

	public function setCurrency($currency)
	{
		$this->currency = $currency;
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

	public function getApproved()
	{
		return $this->approved;
	}

	public function setApproved($approved)
	{
		$this->approved = $approved;
		return $this;
	}
}

?>