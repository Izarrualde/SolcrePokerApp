<?php
namespace Solcre\lmsuy\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 * @ORM\Entity(repositoryClass="Solcre\lmsuy\Repository\BaseRepository")
 * @ORM\Table(name="users")
 */
class UserEntity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;


    /**
     * @ORM\Column(type="string")
     */
    protected $password;


    protected $mobile;


    /**
     * @ORM\Column(type="string")
     */
    protected $email;


    /**
     * @ORM\Column(type="string")
     */
    protected $name;


    /**
     * @ORM\Column(type="string", name="last_name")
     */
    protected $lastname;


    /**
     * @ORM\Column(type="string")
     */
    protected $username;


    /**
     * @ORM\Column(type="decimal")
     */
    protected $multiplier;


    /**
     * @ORM\Column(type="integer", name="is_active")
     */
    protected $isActive;


    /**
     * @ORM\Column(type="decimal")
     */
    protected $hours;


    /**
     * @ORM\Column(type="integer")
     */
    protected $points;


    /**
     * @ORM\Column(type="integer")
     */
    protected $sessions;


    /**
     * @ORM\Column(type="decimal")
     */
    protected $results;


    /**
     * @ORM\Column(type="integer")
     */
    protected $cashin;


    public function __construct(
        $id = null,
        $password = "",
        $mobile = "",
        $email = "",
        $lastname = "",
        $name = "",
        $username = "",
        $multiplier = null,
        $isActive = null,
        $hours = 0,
        $points = 0,
        $sessions = 0,
        $results = 0,
        $cashin = 0
    ) {
        $this->setId($id);
        $this->setPassword($password);
        $this->setMobile($mobile);
        $this->setEmail($email);
        $this->setLastname($lastname);
        $this->setName($name);
        $this->setUsername($username);
        $this->setMultiplier($multiplier);
        $this->setIsActive($isActive);
        $this->setHours($hours);
        $this->setPoints($points);
        $this->setSessions($sessions);
        $this->setResults($results);
        $this->setCashin($cashin);
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

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function getMobile()
    {
        return $this->mobile;
    }


    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }


    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function getMultiplier()
    {
        return $this->multiplier;
    }

    public function setMultiplier($multiplier)
    {
        $this->multiplier = $multiplier;
        return $this;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getHours()
    {
        return $this->hours;
    }

    public function setHours($hours)
    {
        $this->hours = $hours;
        return $this;
    }

    public function getPoints()
    {
        return $this->points;
    }

    public function setPoints($points)
    {
        $this->points = $points;
        return $this;
    }

    public function getSessions()
    {
        return $this->sessions;
    }

    public function setSessions($sessions)
    {
        $this->sessions = $sessions;
        return $this;
    }

    public function getResults()
    {
        return $this->results;
    }

    public function setResults($results)
    {
        $this->results = $results;
        return $this;
    }


    public function getCashin()
    {
        return $this->cashin;
    }

    public function setCashin($cashin)
    {
        $this->cashin = $cashin;
        return $this;
    }

    public function toArray()
    {
        return [
        'id'         => $this->getId(),
        'password'   => $this->getPassword(),
        'email'      => $this->getEmail(),
        'name'       => $this->getName(),
        'lastname'   => $this->getLastname(),
        'username'   => $this->getUsername(),
        'multiplier' => $this->getMultiplier(),
        'sessions'   => $this->getSessions(),
        'isActive'   => $this->getIsActive(),
        'hours'      => $this->getHours(),
        'points'     => $this->getPoints(),
        'results'    => $this->getResults(),
        'cashin'     => $this->getCashin()
        ];
    }
}
