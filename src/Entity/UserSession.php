<?php
Namespace Solcre\lmsuy\Entity;


/**
 * @ORM\Entity(repositoryClass="Solcre\lmsuy\Repository\BaseRepository")
 * @ORM\Table(name="users")
*/
class UserSessionEntity 
{

   /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   */
	protected $id;


	protected $session;


	/**
	 * @ORM\Column(type="integer", name="user_id")
	 */
	protected $idUser;


	/**
	 * @ORM\Column(type="integer",name="is_approved")
	 */
	protected $isApproved;


	/**
	 * @ORM\Column(type="integer", name="points")
	 */
	protected $accumulatedPoints;


	/**
	 * @ORM\Column(type="integer")
	 */
	protected $cashout;


	/**
	 * @ORM\Column(type="datetime", name="start_at")
	 */
	protected $start;


	/**
	 * @ORM\Column(type="datetime", name="end_at")
	 */
	protected $end;


	protected $user;


	public function __construct($id=null, SessionEntity $session = null, $idUser=null, $isApproved=null, $accumulatedPoints=0, $cashout=0, $start=null, $end=null)
	{
		$this->setId($id);
		$this->setSession($session);
		$this->setIdUser($idUser);
		$this->setIsApproved($isApproved);
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
	
	public function getIsApproved() 
	{
		return $this->isApproved;
	}
	
	public function setIsApproved($isApproved){
		$this->isApproved = $isApproved;
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

	public function getUser() 
	{
		return $this->user;
	}
	
	public function setUser(UserEntity $user)
	{
		$this->user = $user;
		return $this;
	}

	public function getIdSession()
	{
		return  ($this->session instanceof SessionEntity) ? $this->session->getIdSession() : null;
	}

	public function getCashin() 
	{
		$cashin = 0;
		$session = $this->getSession();
		$buyins = $session->getSessionBuyins();
		if ($session instanceof SessionEntity) {
			foreach($session->getSessionBuyins() as $buyin) {
				if ($buyin->getSessionUserId() == $this->getId()) {
					$cashin += $buyin->getAmountCash() + $buyin->getAmountCredit();
				}
			}
		} 
		return $cashin;
	}

	public function getTotalCredit()
	{
		$credit = 0;
		$session = $this->getSession();
		$buyins = $session->getSessionBuyins();	
		if ($session instanceof SessionEntity) {
			foreach($session->getSessionBuyins() as $buyin) {
				if ($buyin->getSessionUserId() == $this->getId()) {
					$credit += $buyin->getAmountCredit();
				}
			}
		} 
		return $credit;
	}

	public function getResult()
	{
		return $this->getCashout() - $this->getCashin();
	}
	
	public function getHourPlayed()
	{
		$date1=date_create($this->getEnd());
		$date2=date_create($this->getStart());
		$diff=date_diff($date1, $date2);

		return $diff;
	}

	public function toArray(){
		$ret =  [
			'id' => $this->getId(),
			'idSession' => $this->getSession()->getIdSession(),
			'idUser' => $this->getIdUser(),
			'isApproved' => $this->getIsApproved(),
			'cashout' => $this->getCashout(),
			'startTime' => $this->getStart(),
			'endTime' => $this->getEnd(),
			'cashin' => $this->getCashin(),
			'totalCredit' => $this->getTotalCredit()
		];

		$user = $this->getUser();
		if ($user instanceof UserEntity) {
			$ret['user'] = $user->toArray();
		}

		return $ret;
	}
}

?>