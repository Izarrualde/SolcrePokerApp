<?php
Namespace Solcre\lmsuy\Service;

Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\UserEntity;

class UserService {

	protected $connection;

	public function __construct(ConnectLmsuy_db $connection)
	{
		$this->connection = $connection;
	}

	public function add(UserEntity $user)
	{
		$this->connection->addUser(date('c'), $user->getLastname(), $user->getName(), $user->getUsername(), $user->getMobile(), $user->getEmail(), $user->getPassword(), $user->getMultiplier(), $user->getActive(), $user->getHours(), $user->getPoints(), $user->getResults(), $user->getCashin());
	}

	public function update(UserEntity $user)
	{
		$this->connection-> updateUser($user->getName(), $user->getLastname(), $user->getUsername(), $user->getEmail(), $user->getId());
	}

	public function delete(UserEntity $user)
	{	
		$this->connection->deletePlayer($user->getId());
	}

	public function findOne($id)
	{
		$user = $this->connection->getDatosuserById($id);
		$userObject = new UserEntity($user->id, $user->password, null, $user->email, $user->last_name, $user->name,  $user->username, $user->multiplier, $user->is_active, $user->hours, $user->points, $user->results, $user->cashin);
		return $userObject;
	}

	public function find()
	{
		$user = $this->connection->getDatosUsers();
		$users = array();//

		foreach ($datosUsers as $user) 
		{
			$userObject = new UserEntity($user->id, $user->password, null, $user->email, $user->last_name, $user->name,  $user->username, $user->multiplier, $user->is_active, $user->hours, $user->points, $user->results, $user->cashin);
			
			$users[] = $userObject; 
		}
		return $users;
	}

}

