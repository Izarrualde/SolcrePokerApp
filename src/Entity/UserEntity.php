<?php
Namespace Solcre\PokerApp\Entity;

class UserEntity {
	protected $id;
	protected $password;
	protected $email;
	protected $firstname;
	protected $lastname;
	protected $nickname;
	protected $multiplier;
	protected $active;
	//for cache
	protected $hours;
	protected $points;
	protected $results;
	protected $cashin;

	public function __construct($id=null, $password="", $email="", $lasname="", $firstname="", $multiplier=null, $active=null, $hours=0, $points=0, $results=0, $cashin=0) 
	{
		$this->setId($id);
		$this->setPassword($password);
		$this->setEmail($email);
		$this->setLastname($lastname);
		$this->setFirstName($firstname);
		$this->setMultiplier($multiplier);
		$this->setActive($active);
		$this->setHours($hours);
		$this->setpoints($poins);
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

	public function getEmail() 
	{
		return $this->email;
	}


	public function setEmail($email)
	{
		$this->email = $email;
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

	public function getMultiplier() 
	{
		return $this->multiplier;
	}

	public function setMultiplier($multiplier)
	{
		$this->multiplicador = $multiplier;
		return $this;
	}

	public function getActive() 
	{
		return $this->active;
	}

	public function setActive($active)
	{
		$this->active = $active;
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