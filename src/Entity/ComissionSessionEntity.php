<?php
Namespace Solcre\lmsuy\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 * @ORM\Entity(repositoryClass="Solcre\lmsuy\Repository\BaseRepository")
 * @ORM\Table(name="session_comissions")
 */
class ComissionSessionEntity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
	protected $id;


	/**
	 * @ORM\ManyToOne(targetEntity="Solcre\lmsuy\Entity\SessionEntity", inversedBy="sessionComissions")
	 * @ORM\JoinColumn(name="session_id", referencedColumnName="id")
     */
	protected $session;

	protected $idSession;

    /**
	 * @ORM\Column(type="datetime", name="created_at")
	 */
	protected $hour;


    /**
	 * @ORM\Column(type="integer")
	 */
	protected $comission;


	public function __construct($id=null, $idSession=null, $hour=null, $comission=null, $session=null)
	{
		$this->setId($id);
		$this->setId($idSession);
		$this->setSession($session);
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

	public function getSession()
	{
		return $this->session;
	}

	public function setSession($session)
	{
		$this->session = $session;
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

	public function toArray(){
		return  [
			'id' => $this->getId(),
			'idSession' => $this->getIdSession(),
			'hour' => $this->getHour(),
			'comission' => $this->getComission()
		];
	}

}

?>