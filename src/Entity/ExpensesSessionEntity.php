<?php
namespace Solcre\lmsuy\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 * @ORM\Entity(repositoryClass="Solcre\lmsuy\Repository\BaseRepository")
 * @ORM\Table(name="session_expenses")
 */
class ExpensesSessionEntity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;


    /**
     * @ORM\ManyToOne(targetEntity="Solcre\lmsuy\Entity\SessionEntity", inversedBy="sessionExpenses")
     * @ORM\JoinColumn(name="session_id",                               referencedColumnName="id")
     */
    protected $session;


    /**
     * @ORM\Column(type="string")
     */
    protected $description;


    /**
     * @ORM\Column(type="integer")
     */
    protected $amount;


    public function __construct(
        $id = null,
        $session = null,
        $description = null,
        $amount = null
    ) {
        $this->setId($id);
        $this->setSession($session);
        $this->setDescription($description);
        $this->setAmount($amount);
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

    public function getSession()
    {
        return $this->session;
    }

    public function setSession($session)
    {
        $this->session = $session;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }
    
    // @codeCoverageIgnoreEnd
    public function toArray()
    {
        return  [
        'id'          => $this->getId(),
        'idSession'   => $this->getSession()->getId(),
        'description' => $this->getDescription(),
        'amount'      => $this->getAmount()
        ];
    }
}
