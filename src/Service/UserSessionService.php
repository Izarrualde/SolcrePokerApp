<?php
Namespace Solcre\lmsuy\Service;

Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\UserSession;

class UserSessionService {

	protected $connection;
	protected $userService;
	protected $sessionService;

	public function __construct(ConnectLmsuy_db $connection, UserService $userService, SessionService $sessionService)
	{
		$this->connection = $connection;
		$this->userService = $userService;
		$this->sessionService = $sessionService;
	}

	public function add(UserSession $userSession)
	{
		$this->connection->insertUserInSession(date('c'), $userSession->getAccumulatedPoints(), $userSession->getCashout(), $userSession->getStart(), $userSession->getEnd(), $userSession->getIsApproved(), $userSession->getSession()->getIdSession());
	}

	public function update(UserSession $userSession)
	{
		$this->connection-> updateUserSession($userSession->getAccumulatedPoints(), $userSession->getCashout(), $userSession->getStart(), $userSession->getEnd() , $userSession->getIsApproved() , $userSession->getIdSession(), $userSession->getIdUser(), $userSession->getId());
	}

	public function delete(UserSession $userSession)
	{	
		$this->connection->deleteUser($userSession->getId());
	}

	public function close(UserSession $userSession, $cashout, $end)
	{
		$this->connection->closeUserSession($userSession->getId(), $userSession->getUser()->getId(), $cashout, $userSession->getStart(), $end);
	}

	public function findOne($id)
	{
		$user = $this->connection->getDatosSessionUserById($id);
		var_dump($id);
		$userObject = new UserSession($user->id, /*$sessionService->findOne($connection->getIdSessionbyIdUserSession($id)) */null, $user->user_id, $user->is_approved, $user->points, $user->cashout, $user->start_at, $user->end_at);
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
}