<?php
namespace Solcre\lmsuy\Entity;

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
    protected $sessionDealerTips;


    /**
     * @ORM\OneToMany(targetEntity="Solcre\lmsuy\Entity\ServiceTipSessionEntity", mappedBy="session")
     */
    protected $sessionServiceTips;

    /**
     * @ORM\OneToMany(targetEntity="Solcre\lmsuy\Entity\UserSessionEntity", mappedBy="session")
     */
    protected $sessionUsers;

    /**
     * @ORM\OneToMany(targetEntity="Solcre\lmsuy\Entity\ComissionSessionEntity", mappedBy="session")
     */
    protected $sessionComissions;

    /**
     * @ORM\OneToMany(targetEntity="Solcre\lmsuy\Entity\ExpensesSessionEntity", mappedBy="session")
     */
    protected $sessionExpenses;

    public function __construct(
        $id = null,
        \DateTime $date = null,
        $title = null,
        $description = null,
        $photo = null,
        $seats = null,
        $startTime = null,
        $startTimeReal = null,
        $endTime = null
    ) {
        $this->setId($id);
        $this->setDate($date);
        $this->setTitle($title);
        $this->setDescription($description);
        $this->setPhoto($photo);
        $this->setSeats($seats);
        $this->setStartTime($startTime);
        $this->setStartTimeReal($startTimeReal);
        $this->setEndTime($endTime);
        $this->sessionExpenses    = new ArrayCollection();
        $this->sessionComissions  = new ArrayCollection();
        $this->sessionUsers       = new ArrayCollection();
        $this->sessionDealerTips  = new ArrayCollection();
        $this->sessionServiceTips = new ArrayCollection();
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
        $this->sessionServiceTips=$ServiceTips;
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
        $buyins = [];
        // Recorrer todos los userSession y devolver los buyins.
        // Usar array_map para resolverlo.
        return $buyins;
    }

    public function getSessionExpenses()
    {
        return $this->sessionExpenses;
    }
    
    public function setSessionExpenses($sessionExpenses)
    {
        $this->sessionExpenses=$sessionExpenses;
        return $this;
    }

    protected function getTotalCashout()
    {
        return array_reduce(
            $this->sessionUsers->toArray(),
            function ($cashout, $user) {
                return $cashout + $user->getCashout();
            }
        );
    }

    public function getDealerTipTotal()
    {
        return array_reduce(
            $this->sessionDealerTips->toArray(),
            function ($dealerTipTotal, $tipHour) {
                return $dealerTipTotal + $tipHour->getDealerTip();
            }
        );
    }

    public function getExpensesTotal()
    {
        return array_reduce(
            $this->sessionExpenses->toArray(),
            function ($expensesTotal, $expenditure) {
                return $expensesTotal + $expenditure->getAmount();
            }
        );
    }

    public function getServiceTipTotal()
    {
        return array_reduce(
            $this->sessionServiceTips->toArray(),
            function ($serviceTipTotal, $tipHour) {
                return $serviceTipTotal + $tipHour->getServiceTip();
            }
        );
    }

    public function getComissionTotal()
    {
        return array_reduce(
            $this->sessionComissions->toArray(),
            function ($comissionTotal, $comissionHour) {
                return $comissionTotal + $comissionHour->getComission();
            }
        );
    }

    public function getTotalPlayed()
    {
        return array_reduce(
            $this->getSessionBuyins(),
            function ($amountTotal, $buyin) {
                return $amountTotal +
                $buyin->getAmountCash() +
                $buyin->getAmountCredit();
            }
        );
    }

    public function validateSession($session)
    {
        $total = $session->getTotalCashout() +
        $session->getComissionTotal() +
        $session->getDealerTipTotal()+
        $session-> getServiceTipTotal();
        return $session->getTotalPlayed() == $total;
    }

    public function getActivePlayers()
    {
        $activePlayers = array();
        foreach ($this->sessionUsers as $user) {
            if (!in_array($user->getUser()->getId(), $activePlayers) &&
                ($user->getEnd() == null) &&
                ($user->getStart() != null)) {
                $activePlayers[]= $user->getUser()->getId();
            }
        }
        return $activePlayers;
    }

    public function getTotalDistinctPlayers()
    {
        $distinctPlayers = array();
        foreach ($this->sessionUsers as $user) {
            if (!in_array($user->getUser()->getId(), $distinctPlayers)) {
                $distinctPlayers[]= $user->getUser()->getId();
            }
        }
        return count($distinctPlayers);
    }

    public function toArray()
    {
        return  [
        'id'                 => $this->getId(),
        'created_at'         => $this->getDate(),
        'title'              => $this->getTitle(),
        'description'        => $this->getDescription(),
        'startTime'          => $this->getStartTime(),
        'startTimeReal'      => $this->getStartTimeReal(),
        'countActivePlayers' => count($this->getActivePlayers()),
        'activePlayers'      => $this->getActivePlayers(),
        'distinctPlayers'    => $this->getTotalDistinctPlayers(),
        'seats'              => $this->getSeats(),
        'endTime'            => $this->getEndTime(),
        'comissionTotal'     => $this->getComissionTotal(),
        'expensesTotal'      => $this->getExpensesTotal(),
        'dealerTipTotal'     => $this->getDealerTipTotal(),
        'serviceTipTotal'    => $this->getServiceTipTotal()
        ];
    }
}
