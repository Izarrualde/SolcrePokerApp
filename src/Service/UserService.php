<?php
Namespace Solcre\lmsuy\Service;

Use \Solcre\lmsuy\Entity\UserEntity;
Use Doctrine\ORM\EntityManager;

class UserService extends BaseService {

	public function __construct(EntityManager $em)
	{
		parent::__construct($em);
	}

	public function add($data, $strategies = null)
	{
		/*
		$this->connection->addUser(date('c'), $user->getLastname(), $user->getName(), $user->getUsername(), $user->getMobile(), $user->getEmail(), $user->getPassword(), $user->getMultiplier(), $user->getIsActive(), $user->getHours(), $user->getPoints(), $user->getSessions(), $user->getResults(), $user->getCashin());
		*/

		$user = new UserEntity();
		$user->setPassword($data['password']);
		$user->setName($data['firstname']);
		$user->setLastname($data['lastname']);
		$user->setEmail($data['email']);
		$user->setUsername($data['username']);
		$user->setMultiplier($data['multiplier']);
		$user->setIsActive($data['active']);
		$user->setHours($data['hours']);
		$user->setPoints($data['points']);
		$user->setSessions($data['sessions']);
		$user->setResults($data['results']);
		$user->setCashin($data['cashin']);

		// Aca le decimos al Entity Manager que persista el objeto en la base datos
		$this->entityManager->persist($user);

		// En este metodo lo que hacemos es ejecutar las consultas.
        $this->entityManager->flush($user);
	}

	public function update($data, $strategies = null)  // poner los parametros del  updateUser todos los necesarios.
	{
		/*
		$this->connection-> updateUser($user->getName(), $user->getLastname(), $user->getUsername(), $user->getEmail(), $user->getPassword(), $user->getMultiplier(), $user->getIsActive(), $user->getSessions(), $user->getId());
		*/

		$user = parent::fetch($data['id']);
		$user->setName($data['name']);
		$user->setLastname($data['lastname']);
		$user->setEmail($data['email']);
		$user->setUsername($data['username']);

		// Aca le decimos al Entity Manager que persista el objeto en la base datos
		$this->entityManager->persist($user);

		// En este metodo lo que hacemos es ejecutar las consultas.
        $this->entityManager->flush($user);
	}

	public function delete($id, $entityObj = null)
	{	
		$user = $this->entityManager->getReference('Solcre\lmsuy\Entity\UserEntity', $id);

		$this->entityManager->remove($user);
		$this->entityManager->flush();
	}
	/*
	public function findOne($id)
	{
		$user = $this->connection->getDatosuserById($id);
		$userObject = new UserEntity($user->id, $user->password, null, $user->email, $user->last_name, $user->name,  $user->username, $user->multiplier, $user->is_active, $user->hours, $user->points, $user->sessions, $user->results, $user->cashin);
		return $userObject;
	}
	*/
	/*
	public function find()
	{
		$datosUsers = $this->connection->getDatosUsers();
		$users = array();//

		foreach ($datosUsers as $user) 
		{
			$userObject = new UserEntity($user->id, $user->password, null, $user->email, $user->last_name, $user->name,  $user->username, $user->multiplier, $user->is_active, $user->hours, $user->points, $user->results, $user->cashin);
			
			$users[] = $userObject; 
		}
		return $users;
	}
	*/
}

