<?php
namespace Solcre\lmsuy\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 * @ORM\Entity(repositoryClass="Solcre\lmsuy\Repository\BaseRepository")
 * @ORM\Table(name="session_service_tips")
 */
class ServiceTipSessionEntity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;


    /**
     * @ORM\ManyToOne(targetEntity="Solcre\lmsuy\Entity\SessionEntity", inversedBy="sessionServiceTips")
     * @ORM\JoinColumn(name="session_id",                               referencedColumnName="id")
     */
    protected $session;

    protected $idSession;


    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $hour;


    /**
     * @ORM\Column(type="integer", name="service_tip")
     */
    protected $serviceTip;


    public function __construct(
        $id = null,
        $idSession = null,
        $hour = "",
        $tip = null,
        $session = null
    ) {
        $this->setId($id);
        $this->setIdSession($idSession);
        $this->setHour($hour);
        $this->setServiceTip($tip);
        $this->setSession($session);
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

    public function getServiceTip()
    {
        return $this->serviceTip;
    }

    public function setServiceTip($tip)
    {
        $this->serviceTip = $tip;
        return $this;
    }

    public function toArray()
    {
        return  [
        'id'         => $this->getId(),
        'idSession'  => $this->getSession()->getId(),
        'hour'       => $this->getHour(),
        'serviceTip' => $this->getServiceTip()
        ];
    }
}
