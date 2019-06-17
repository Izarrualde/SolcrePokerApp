<?php
Namespace Solcre\lmsuy\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Embeddable
 * @ORM\Entity(repositoryClass="Solcre\lmsuy\Repository\BaseRepository")
 * @ORM\Table(name="sessions_users")
 */
class UserSessionEntity 
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
	protected $id;


	/**
	 * @ORM\ManyToOne(targetEntity="Solcre\lmsuy\Entity\SessionEntity", inversedBy="sessionUsers")
	 * @ORM\JoinColumn(name="session_id", referencedColumnName="id")
     */
	protected $session;


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



   	/**
     * @ORM\ManyToOne(targetEntity="Solcre\lmsuy\Entity\UserEntity", inversedBy="sessionUsers")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
	protected $user;


    /**  
     * One User Session has many Buyins. This is the inverse side.
     * @ORM\OneToMany(targetEntity="Solcre\lmsuy\Entity\BuyinSessionEntity", mappedBy="userSession")
     */
	protected $buyins;


	public function __construct($id=null, SessionEntity $session = null, $idUser=null, $isApproved=null, $accumulatedPoints=0, $cashout=0, $start=null, $end=null, UserEntity $user=null)
	{
		$this->setId($id);
		$this->setSession($session);
		$this->setIdUser($idUser);
		$this->setIsApproved($isApproved);
		$this->setAccumulatedPoints($accumulatedPoints);
		$this->setCashout($cashout);
		$this->setStart($start);
		$this->setEnd($end);
		/*if ($user instanceof UserEntity)
		{
			$this->setUser($user);	
		} 
		else
		{
			$userObject = new UserEntity();
			$this->setUser($userObject);
		} */
		$this->setUser($user);
		$this->buyins = new ArrayCollection();
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
		return $this->accumulatedPoints;
	}
	
	public function setAccumulatedPoints($accumulatedPoints)
	{
		$this->accumulatedPoints = $accumulatedPoints;
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
		/*If ($this->end != null)
		{
			return $this->end->format('d-m-Y');	
		}*/
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
	
	public function setUser(UserEntity $user = null)
	{
		$this->user = $user;
		return $this;
	}

	public function getBuyins() 
	{
		return $this->buyins;
	}
	
	public function setBuyins($buyins)
	{
		$this->buyins = $buyins;
		return $this;
	}

public function getCashin()
    {
        $cashin = 0;
            $buyins = $this->getBuyins()->toArray();

            foreach($buyins as $buyin) {
                    $cashin += $buyin->getAmountCash() + $buyin->getAmountCredit();
            }
       
        return $cashin;
    }



	public function getTotalCredit()
	{
		$credit = 0;
		 	$buyins = $this->getBuyins()->toArray();

			foreach($buyins as $buyin) {
					$credit += $buyin->getAmountCredit();
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
			'idSession' => $this->getSession()->getId(),
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
		$session = $this->getSession();

		if ($session instanceof SessionEntity) {
			$ret['session'] = $session->toArray();
		}

		return $ret;
	}
}

?>