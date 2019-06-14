<?php
Namespace Solcre\lmsuy\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 * @ORM\Entity(repositoryClass="Solcre\lmsuy\Repository\BuyinSessionRepository")
 * @ORM\Table(name="session_buyins")
 */
class BuyinSessionEntity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
	protected $id;

	protected $idSession;

	protected $sessionUserId;


	/**
	 * @ORM\Column(type="integer", name="amount_of_cash_money")
	 */
	protected $amountCash;


	/**
	 * @ORM\Column(type="integer", name="amount_of_credit_money")
	 */
	protected $amountCredit;


	/**
	 * @ORM\Column(type="integer", name="currency_id")
	 */
	protected $currency;

	/**
	 * @ORM\Column(type="datetime", name="created_at")
	 */
	protected $hour;


	/**
	 * @ORM\Column(type="integer", name="approved")
	 */
	protected $isApproved;


	/**
     * @ORM\ManyToOne(targetEntity="Solcre\lmsuy\Entity\UserSessionEntity", inversedBy="buyins")
     * @ORM\JoinColumn(name="session_user_id", referencedColumnName="id")
     */
	protected $userSession;


	public function __construct($id=null, $sessionUserId=null, $amountCash=null, $amountCredit=null, $currency=null, $hour=null, $isApproved=null)
	{
		$this->setId($id);
		$this->setSessionUserId($sessionUserId);
		$this->setamountCash($amountCash);
		$this->setamountCredit($amountCredit);
		$this->setCurrency($currency);
		$this->setHour($hour);
		$this->setIsApproved($isApproved);
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
/*
	public function getIdSession()
	{
		return $this->idSession;
	}

	public function setIdSession($idSession)
	{
		$this->idSession = $idSession;
		return $this;
	}
*/

	public function getSessionUserId() 
	{
		return $this->sessionUserId;
	}

	public function setSessionUserId($sessionUserId)
	{
		$this->sessionUserId = $sessionUserId;
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

	public function getAmountCredit()
	{
		return $this->amountCredit;
	}

	public function setAmountCredit($amountCredit)
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

	public function getIsApproved()
	{
		return $this->isApproved;
	}

	public function setIsApproved($isApproved)
	{
		$this->isApproved = $isApproved;
		return $this;
	}

	public function getSession()
	{
		return $this->getUserSession()->getSession();
	}
/*
	public function setSession(SessionEntity $session)
	{
		$this->session = $session;
		return $this;
	}
*/

	public function getUserSession()
	{
		return $this->userSession;
	}

	public function setUserSession(UserSession $userSession)
	{
		$this->userSession = $userSession;
		return $this;
	}

	public function toArray(){
		$ret = [
			'id' => $this->getId(),
			'idSession' => $this->getIdSession(),
			'amountCash' => $this->getAmountCash(),
			'amountCredit' => $this->getAmountCredit(),
			'hour' => $this->getHour(),
		];

		$session = $this->getSession();

		if ($session instanceof SessionEntity) {
			$ret['session'] = $session->toArray();
		}

		$userSession = $this->getUserSession();
		if ($userSession instanceof UserSession) {
			$ret['user_session'] = $userSession->toArray();
		}

		return $ret;
	}

}



?>