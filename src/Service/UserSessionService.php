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
		$session = $this->entityManager->getReference('Solcre\lmsuy\Entity\SessionEntity', $data['idSession']);
		$user = $this->entityManager->getReference('Solcre\lmsuy\Entity\UserEntity', $data['idUser']);

		$data['start'] = new \DateTime($data['start']);
		
		$userSession = new UserSessionEntity();

		$userSession->setSession($session);
		$userSession->setIdUser($data['idUser']);
		$userSession->setIsApproved($data['isApproved']);
		$userSession->setAccumulatedPoints((int)$data['points']);
		$userSession->setStart($data['start']);
		$userSession->setUser($user);
		$this->entityManager->getConnection()->getConfiguration()->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
		$this->entityManager->persist($userSession);
		$this->entityManager->flush($userSession);
	}

	public function update($data, $strategies = null)
	{
		$userSession = parent::fetch($data['id']);
		$userSession->setAccumulatedPoints($data['accumulatedPoints']);
		$userSession->setCashout($data['cashout']);
		$userSession->setStart($data['start']);
		$userSession->setEnd($data['end']);
		$userSession->setIsApproved($data['isApproved']);
		$userSession->setSession($this->entityManager->getReference('Solcre\lmsuy\Entity\SessionEntity', $data['idSession']));  
		$userSession->setIdSession($data['idSession']);
		$userSession->setIdUser($data['idUser']);

		$this->entityManager->persist($userSession);
		$this->entityManager->flush($userSession);
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

		$this->entityManager->persist($userSession);
		$this->entityManager->flush($userSession);
	}
}