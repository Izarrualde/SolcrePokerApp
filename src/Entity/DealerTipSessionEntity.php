<?php
namespace Solcre\lmsuy\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 * @ORM\Entity(repositoryClass="Solcre\lmsuy\Repository\BaseRepository")
 * @ORM\Table(name="session_dealer_tips")

 */
class DealerTipSessionEntity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Solcre\lmsuy\Entity\SessionEntity", inversedBy="sessionDealerTips")
     * @ORM\JoinColumn(name="session_id",                               referencedColumnName="id")
     */
    protected $session;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $hour;


    /**
     * @ORM\Column(type="integer", name="dealer_tip")
     */
    protected $dealerTip;


    public function __construct(
        $id = null,
        $hour = null,
        $tip = null,
        $session = null
    ) {
        $this->setId($id);
        $this->setSession($session);
        $this->setHour($hour);
        $this->setDealerTip($tip);
    }

    // @codeCoverageIgnoreStart
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
        return $this->getSession()->getId();
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

    public function getDealerTip()
    {
        return $this->dealerTip;
    }

    public function setDealerTip($tip)
    {
        $this->dealerTip = $tip;
        return $this;
    }

    // @codeCoverageIgnoreEnd
    public function toArray()
    {
        return  [
        'id'        => $this->getId(),
        'idSession' => $this->getSession()->getId(),
        'hour'      => $this->getHour(),
        'dealerTip' => $this->getDealerTip()
        ];
    }
}
