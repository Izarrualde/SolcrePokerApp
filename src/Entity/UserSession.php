<?php
Namespace Solcre\PokerApp\Entity;
class UserSession 
{
	protected $id;
	protected $session;
	protected $idUser;
	protected $approved;
	protected $accumulatedPoints;
	protected $cashout;
	public $start;
	protected $end;
	public function __construct($id=null, SessionEntity $session = null, $idUser=null, $approved=null, $accumulatedPoints=0, $cashout=0, $start=null, $end=null)
	{
		$this->setId($id);
		$this->setSession($session);
		$this->setIdUser($idUser);
		$this->setApproved($approved);
		$this->setAccumulatedPoints($accumulatedPoints);
		$this->setCashout($cashout);
		$this->setStart($start);
		$this->setEnd($end);
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
	
	public function getSession() 
	{
		return $this->session;
	}
    
    public function setSession(SessionEntity $session = null) 
    {
		return $this->session = $session;
	}

	public function getIdUser() 
	{
		return $this->idUser;
	}
	
	public function setIdUser($idUser)
	{
		$this->idUser = $idUser;
		return $this;
	}
	
	public function getApproved() 
	{
		return $this->approved;
	}
	
	public function setApproved($approved){
		$this->approved = $approved;
		return $this;
	}
	
	public function getAccumulatedPoints() 
	{
		return $this->AccumulatedPoints;
	}
	
	public function setAccumulatedPoints($AccumulatedPoints)
	{
		$this->AccumulatedPoints = $AccumulatedPoints;
		return $this;
	}
	
	public function getCashout() 
	{
		return $this->cashout;
	}
	
	public function setCashout($cashout)
	{
		$this->cashout = $cashout;
		return $this;
	}
	
	public function getStart() 
	{
		return $this->start;
	}
	
	public function setStart($start)
	{
		$this->start = $start;
		return $this;
	}
	
	public function getEnd() 
	{
		return $this->end;
	}
	
	public function setEnd($end)
	{
		$this->end = $end;
		return $this;
	}

	public function getCashin() 
	{
		$cashin = 0;
		$session = $this->getSession();
		if ($session instanceof SessionEntity) {
			foreach($session->getSessionBuyins() as $buyin) {
				if ($buyin->getIdPlayer() == $this->getId()) {
					$cashin += $buyin->getAmountCash() + $buyin->getAmountCredit();
				}
			}
		} 
		return $cashin;
	}

	public function getResult()
	{
		return $this->getCashout() - $this->getCashin();
	}
	
	protected function getHourPlayed()
	{
		return dateDiff($this->getEnd(), $this->getStart());
	}
}

?>