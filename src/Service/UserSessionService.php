<?php
Namespace Solcre\lmsuy\Service;

Use \Solcre\lmsuy\Entity\UserSessionEntity;
Use \Solcre\lmsuy\Entity\UserEntity;
Use Doctrine\ORM\EntityManager;

class UserSessionService extends BaseService {

	public function __construct(EntityManager $em)
	{
		parent::__construct($em);
	}


	public function add($data, $strategies = null)
	{
		/*
		return $this->connection->insertUserInSession(date('c'), $userSession->getAccumulatedPoints(), $userSession->getCashout(), $userSession->getStart(), $userSession->getEnd(), $userSession->getIsApproved(), $userSession->getSession()->getIdSession(), $userSession->getIdUser());
		*/

		$userSession = new UserSessionEntity();
		$userSession->setAccumulatedPoints($data['accumulatedPoints']);
		$userSession->setCashout($data['cashout']);
		$userSession->setStart($data['start']);
		$userSession->setEnd($data['end']);
		$userSession->setIsApproved($data['isApproved']);
		//$userSession->setSession(); // como?
		$userSession->setIdSession($data['idSession']);
		$userSession->setIdUser($data['idUser']);

		$this->EntityManager->persist($userSession);
		$this->EntityManager->flush($userSession);
	}

	public function update($data, $strategies = null)
	{
		$userSession = parent::fetch($data['id']);
		$userSession->setAccumulatedPoints($data['accumulatedPoints']);
		$userSession->setCashout($data['cashout']);
		$userSession->setStart($data['start']);
		$userSession->setEnd($data['end']);
		$userSession->setIsApproved($data['isApproved']);
		// $userSession->setSession(); // como? 
		$userSession->setIdSession($data['idSession']);
		$userSession->setIdUser($data['idUser']);

		$this->EntityManager->persist($userSession);
		$this->EntityManager->flush($userSession);
	}

	public function delete($id, $entityObj = null)
	{	
		$userSession = $this->entityManager->getReference('Solcre\lmsuy\Entity\UserSessionEntity', $id);

		$this->entityManager->remove($userSession);
		$this->entityManager->flush();
	}


	public function close($data, $strategies = null)
	{
		$this->connection->closeUserSession($userSession->getId(), $userSession->getUser()->getId(), $cashout, $userSession->getStart(), $end);

		$userSession = parent::fetch($data['id']);
		$userSession->setEnd($data['end']);
		$userSession->setCashout($data['cashout']);

		$date1=date_create($userSession->getEndTime());
		$date2=date_create($userSession->getStartTime());
		$minutes=date_diff($date1, $date2)->format('%i');
		$roundedMinutes=floor((($minutes/60)/.25))*.25;
		$hours=date_diff($date1, $date2)->format('%h') + $roundedMinutes;

		$user = parent::fetch($data['idUser']);
		$user->setHours($user->getHours()+$hours);
		// $sql="UPDATE users SET hours=hours+".$hours." WHERE id='$idUser'";

		$this->EntityManager->persist($userSession);
		$this->EntityManager->flush($userSession);
	}

	/*
	public function findOne($id)
	{
		$user = $this->connection->getDatosSessionUserById($id);
		$userObject = new UserSession($user->id, /*$sessionService->findOne($connection->getIdSessionbyIdUserSession($id)) *//*null, $user->user_id, $user->is_approved, $user->points, $user->cashout, $user->start_at, $user->end_at);
		$this->findEntities($userObject);
		return $userObject;
	}

	public function find($idSession)
	{
		$datosUsersSession = $this->connection->getDatosSessionsUsers($idSession);
		$users = array();

		foreach ($datosUsersSession as $userSession) 
		{
			$userObject = new UserSession($userSession->id, null, $userSession->user_id, $userSession->is_approved, $userSession->points, $userSession->cashout, $userSession->start_at, $userSession->end_at);
			$this->findEntities($userObject);
			$users[] = $userObject; 
		}
		return $users;
	}

	private function findEntities(UserSession $userSession) {
		$idUser = $userSession->getIdUser();
		$user = $this->userService->findOne($idUser);
		$userSession->setUser($user);

		//agrego lo mismo para session.
		$idSession = $this->connection->getIdSessionbyIdUserSession($userSession->getId());
		$session = $this->sessionService->findOne($idSession);
		$userSession->setSession($session);
	}
	*/
}