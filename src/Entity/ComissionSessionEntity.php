<?php
Namespace Solcre\lmsuy\Entity;

/**
 * @ORM\Entity(repositoryClass="Solcre\lmsuy\Repository\BaseRepository")
 * @ORM\Table(name="users")
*/
class ComissionSessionEntity
{

   /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   */
	protected $id;


	/**
	 * @ORM\Column(type="integer", name="session_id")
	 */
	protected $idSession;


	/**
	 * @ORM\Column(type="datetime", name="crated_at")
	 */
	protected $hour;


	/**
	 * @ORM\Column(type="integer")
	 */
	protected $comission;


	public function __construct($id=null, $idSession=null, $hour=null, $comission=null)
	{
		$this->setId($id);
		$this->setIdSession($idSession);
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