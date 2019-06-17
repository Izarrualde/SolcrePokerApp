<?php
Namespace Solcre\lmsuy\Entity;

use Solcre\lmsuy\Exception\UserAlreadyAddedException;
use Solcre\lmsuy\Exception\SessionFullException;
use Solcre\lmsuy\Exception\PlayerNotFoundException;
use Solcre\lmsuy\Exception\InsufficientBuyinException;
use Solcre\lmsuy\Exception\ComissionAlreadyAddedException;
use Solcre\lmsuy\Exception\DealerTipAlreadyAddedException;
use Solcre\lmsuy\Exception\ServiceTipAlreadyAddedException;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Solcre\lmsuy\Repository\BaseRepository")
 * @ORM\Table(name="sessions")
*/
class SessionEntity 
{

   /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="IDENTITY")
   */
	protected $id; 


	/**
	 * @ORM\Column(type="datetime", name="created_at")
	 */
	protected $date;


	/**
	 * @ORM\Column(type="string")
	 */
	protected $title;


	/**
	 * @ORM\Column(type="string")
	 */
	protected $description;


	protected $photo;


	/**
	 * @ORM\Column(type="integer", name="count_of_seats")
	 */
	protected $seats;


	protected $seatsWaiting;
	protected $reserveWaiting;


	/**
	 * @ORM\Column(type="datetime", name="start_at")
	 */
	protected $startTime;


	/**
	 * @ORM\Column(type="datetime", name="real_start_at")
	 */
	protected $startTimeReal;


	/**
	 * @ORM\Column(type="datetime", name="end_at")
	 */
	protected $endTime;

	/**
	 * @ORM\OneToMany(targetEntity="Solcre\lmsuy\Entity\DealerTipSessionEntity", mappedBy="session")
     */		
	public $sessionDealerTips;


	/**
	 * @ORM\OneToMany(targetEntity="Solcre\lmsuy\Entity\ServiceTipSessionEntity", mappedBy="session")
     */		
	public $sessionServiceTips;

	/**
	 * @ORM\OneToMany(targetEntity="Solcre\lmsuy\Entity\UserSessionEntity", mappedBy="session")
     */	
	public $sessionUsers;

	/**
	 * @ORM\OneToMany(targetEntity="Solcre\lmsuy\Entity\ComissionSessionEntity", mappedBy="session")
     */
	public $sessionComissions;


	public $sessionBuyins;


	public function __construct($id=null, DateTime $date=null, $title="", $description="", $photo=null, $seats=null, $seatsWaiting=null, $reserveWaiting=null, $startTime=null, $startTimeReal=null, $endTime=null)
	{
		$this->setId($id);
		$this->setDate($date);
		$this->setTitle($title);
		$this->setDescription($description);
		$this->setPhoto($photo);
		$this->setSeats($seats);
		$this->setSeatsWaiting($seatsWaiting);
		$this->setReserveWaiting($reserveWaiting);
		$this->setStartTime($startTime);
		$this->setStartTimeReal($startTimeReal);
		$this->setEndTime($endTime);
		$this->sessionComissions = new ArrayCollection();
		$this->sessionBuyins = new ArrayCollection();
		$this->sessionUsers = new ArrayCollection();
		$this->sessionDealerTips = new ArrayCollection();
		$this->sessionServiceTips = new ArrayCollection();
		//$this->setSessionDealerTips($sessionDealerTips);
		//$this->setSessionServiceTips($sessionServiceTips);
		//$this->setSessionUsers($sessionUsers);
		//$this->setSessionComissions($sessionComissions);
		//$this->setSessionBuyins($sessionBuyins);	
	}


	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id=$id;
		return $this;
	}

	public function getDate()
	{
		return $this->date;
	}


	public function setDate($date)
	{
		$this->date=$date;
		return $this;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function setTitle($title)
	{
		$this->title=$title;
		return $this;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function setDescription($description)
	{
		$this->description=$description;
		return $this;
	}

	public function getPhoto()
	{
		return $this->photo;
	}

	public function setPhoto($photo)
	{
		$this->photo=$photo;
		return $this;
	}

	public function getSeats()
	{
		return $this->seats;
	}

	public function setSeats($seats)
	{
		$this->seats=$seats;
		return $this;
	}

	public function getSeatsWaiting()
	{
		return $this->seatsWaiting;
	}

	public function setSeatsWaiting($seatsWaiting)
	{
		$this->seatsWaiting=$seatsWaiting;
	return $this;
	}

	public function getReserveWaiting()
	{
		return $this->reserveWaiting;
	}

	public function setReserveWaiting($reserveWaiting)
	{
		$this->reserveWaiting=$reserveWaiting;
		return $this;
	}

	public function getStartTime()
	{
		return $this->startTime;
	}

	public function setStartTime($startTime)
	{
		$this->startTime=$startTime;
		return $this;
	}

	public function getStartTimeReal()
	{
		return $this->startTimeReal;
	}

	public function setStartTimeReal($startTimeReal)
	{
		$this->startTimeReal=$startTimeReal;
		return $this;
	}

	public function getEndTime()
	{
		return $this->endTime;
	}

	public function setEndTime($endTime)
	{
		$this->endTime=$endTime;
		return $this;
	}


	public function getSessionDealerTips()
	{
		return $this->sessionDealerTips;
	}

	public function setSessionDealerTips($dealerTips)
	{
		$this->sessionDealerTips=$dealerTips;
		return $this;
	}

	public function getSessionServiceTips()
	{
		return $this->sessionServiceTips;
	}

	public function setSessionServiceTips($ServiceTips)
	{
		$this->sesionServiceTips=$ServiceTips;
		return $this;
	}

    public function getSessionUsers()
    {
		return $this->sessionUsers;
	}

    public function setSessionUsers($sessionUsers)
    {
		$this->sessionUsers=$sessionUsers;
		return $this;
	}

	public function getSessionComissions()
	{
		return $this->sessionComissions;
	}

    public function setSessionComissions($sessionComissions)
    {
		$this->sessionComissions=$sessionComissions;
		return $this;
	}
    
    public function getSessionBuyins()
    {
		return $this->sessionBuyins;
	}
	
    public function setSessionBuyins($sessionBuyins)
    {
		$this->sessionBuyins=$sessionBuyins;
		return $this;
	}	


	public function getConfirmedPlayers()
	{
		return count($this->sessionUsers);
	}

/*--------------------------------------------------------------------------------------------------------------*/


	public function getTotalCashout()
	{
		$cashout = 0;
		foreach ($this->sessionUsers as $user) 
		{
			/** @var UsersSession $user */
			$cashout +=  $user->getCashout(); 
		}
		return $cashout;
	}


// --------------------------------------------------------------------------------------------------

	public function getDealerTipTotal ()
	{
		$dealerTipTotal = 0;
		foreach ($this->sessionDealerTips as $tipHour) 
		{
			$dealerTipTotal += $tipHour->getDealerTip();  
		}
		return $dealerTipTotal;
	}  


	protected function getDealerTipIds() 
	{
		return array_map (function(DealerTipSessionEntity $tip) { return $tip->getId();}, $this->sessionDealerTips);
	}

	protected function isAddedDealerTip(DealerTipSessionEntity $tip)
	{
		$idsDealerTips = $this->getDealerTipIds();
		return in_array($tip->getId(), $idsDealerTips);
	}

	public function addDealerTips(array $tips)
	{
		foreach ($tips as $tip) 
		{
			$this->addDealerTip($tip);
		}
	}

	public function addDealerTip(DealerTipSessionEntity $tip) 
	{
		if ($this->isAddedDealerTip($tip)) 
		{
			throw new DealerTipAlreadyAddedException();	
		} elseif (isset($this->sessionDealerTips[$tip->getHour()]) && $this->sessionDealerTips[$tip->getHour()] instanceof DealerTipSession) 
		{
			throw new DealerTipAlreadyAddedException();
		}
		$this->sessionDealerTips[$tip->getHour()] = $tip;
	}


//------------------------------------------------------------------------------------------------------

	public function getServiceTipTotal ()
	{
		$serviceTipTotal = 0;
		foreach ($this->sessionServiceTips as $tipHour) 
		{
			$serviceTipTotal += $tipHour->getServiceTip();  
		}
		return $serviceTipTotal;
	}  


	protected function getServiceTipIds() 
	{
		return array_map (function(ServiceTipSessionEntity $tip) {
			return $tip->getId();

		}, $this->sessionServiceTips);
	}


	protected function isAddedServiceTip(ServiceTipSessionEntity $tip)
	{
		$idsServiceTips = $this->getServiceTipIds();
		return in_array($tip->getId(), $idsServiceTips);
	}

	public function addServiceTips(array $tips)
	{
		foreach ($tips as $tip) {
			$this->addServiceTip($tip);
		}
	}

	public function addServiceTip(ServiceTipSessionEntity $tip) 
	{
		if ($this->isAddedServiceTip($tip)) 
		{
			throw new ServiceTipAlreadyAddedException();	
		} elseif (isset($this->sessionServiceTips[$tip->getHour()]) && $this->sessionServiceTips[$tip->getHour()] instanceof ServiceTipSession) 
		{
			throw new ServiceTipAlreadyAddedException();
		}
		$this->sessionServiceTips[$tip->getHour()] = $tip;
	}

//------------------------------------------------------------------------------------------------------


	public function getComissionTotal ()
	{
		$comissionTotal = 0;
		foreach ($this->sessionComissions as $comissionHour) 
		{
			$comissionTotal += $comissionHour->getComission();  //getComision() aparece en lista pero estaria usando el de esta clase no el de UserSesion !!!
		}
		return $comissionTotal;
	}

	protected function getComissionIds() 
	{
		return array_map (function(ComissionSessionEntity $comission) {
			return $comission->getId();

		}, $this->sessionComissions);
	}


	protected function isAddedComission(ComissionSessionEntity $comission)
	{
		$ComissionsIds = $this->getComissionIds();
		return in_array($comission->getId(), $ComissionsIds);
	}

	public function addComissions(array $comissions)
	{
		foreach ($comissions as $comission) {
			$this->addComission($comission);
		}
	}

	protected function addComission(ComissionSessionEntity $comission) 
	{
		if ($this->isAddedComission($comission)) {
			throw new ComissionAlreadyAddedException();	
		} elseif (isset($this->sessionComissions[$comission->getHour()]) && $this->sessionComissions[$comission->getHour()] instanceof ComissionSession) 
		{
			throw new ComissionAlreadyAddedException();
		}
		$this->sessionComissions[$comission->getHour()] = $comission;
	}

	private function getUserIds() 
	{
		return array_map(function (UserSessionEntity $user) {  
		    		return $user->getId();
		}, $this->sessionUsers);
	}

	protected function isAdded(UserSessionEntity $user) 
	{
		$usersIds = $this->getUserIds();
		return in_array($user->getId(), $usersIds);
	}

	public function addUsers(array $users) 
	{
		foreach ($users as $user) 
		{
			$this->addUser($user);
		}
	}
		
	public function addUser(UserSessionEntity $user) 
	{
		if (($this->getSeats() - $this->getConfirmedPlayers()) == 0) 
		{
			throw new SessionFullException();
		} elseif ($this->isAdded($user, $this->sessionUsers)) 
		{
			throw new UserAlreadyAddedException();
		}  
		$this->sessionUsers[] = $user;
	}

	public function isPlayer($id)
	{
		return in_array($id, $this->getUserIds());
	}


//-----------------------------------------------------------------------------------------------------------------------------------
/*
	public function addBuyin(BuyinSessionEntity $buyin)
	{
		if (!$this->isPlayer($buyin->getIdPlayer())) 
		{
			throw new PlayerNotFoundException();
		}
		elseif (($buyin->getAmountCash() + $buyin->getAmountCredit()) < 100) 
		{
			throw new InsufficientBuyinException();
		}
		$this->sessionBuyins[] = $buyin;
	}

	public function addBuyins(array $buyins)
	{
		foreach ($buyins as $buyin)
		{
			$this->addBuyin($buyin);
		}
	}
*/
	public function getTotalPlayed()
	{
	/* esta funcion recibe el buyin de los jugadores de la sesion y devuelve el total jugado */

		$amountTotal = 0;
		foreach ($this->sessionBuyins as $buyin) 
		{
			$amountTotal += $buyin->getAmountCash() + $buyin->getAmountCredit();
		}
		return $amountTotal;
	}



	public function validateSession($session)
	{
		If ($session->getTotalPlayed() == $session->getTotalCashout() + $session->getComissionTotal() + $session->getDealerTipTotal()+ $session-> getServiceTipTotal())
		{
			return true;
		} else 
		  {
			return false;
		  }
	}

	public function getActivePlayers()
	{
		$activePlayers = array();
		foreach ($this->sessionUsers as $user) 
		{
			if (!in_array($user->getIdUser(), $activePlayers) && ($user->getEnd() == null) && ($user->getStart() != null)) 
			$activePlayers[]= $user->getIdUser();
		}
		return count($activePlayers);
	}

	public function getTotalDistinctPlayers()
	{
		$distinctPlayers = array();
		foreach ($this->sessionUsers as $user) 
		{
			if (!in_array($user->getIdUser(), $distinctPlayers))
			$distinctPlayers[]= $user->getIdUser();
		}
		return count($distinctPlayers);
	}

	public function toArray(){
		return  [
			'id' => $this->getId(),
			'created_at' => $this->getDate(),
			'title' => $this->getTitle(),
			'description' => $this->getDescription(),
			'startTime' => $this->getStartTime(),
			'startTimeReal' => $this->getStartTimeReal(),
			'activePlayers' => $this->getActivePlayers(),
			'distinctPlayers' => $this->getTotalDistinctPlayers(),
			'seats' => $this->getSeats(),
			'endTime' => $this->getEndTime()
		];
	}
	
}
?>