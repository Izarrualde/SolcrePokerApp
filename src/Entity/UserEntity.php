<?php
Namespace Solcre\lmsuy\Entity;

class UserEntity 
{
	protected $id;
	protected $password;
	protected $mobile;
	protected $email;
	protected $name;
	protected $lastname;
	protected $username;
	protected $multiplier;
	protected $isActive;
	//for cache
	protected $hours;
	protected $points;
	protected $results;
	protected $cashin;

	public function __construct($id=null, $password="", $mobile="", $email="", $lastname="", $name="",  $username="", $multiplier=null, $isActive=null, $hours=0, $points=0, $results=0, $cashin=0) 
	{
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
		$this->setpoints($points);
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

	public function getUser() 
	{
		return $this->user;
	}

	public function setUser($user)
	{
		$this->user = $user;
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
		$this->multiplicador = $multiplier;
		return $this;
	}

	public function getIs_active() 
	{
		return $this->active;
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


}

?>