<?php
Namespace Solcre\lmsuy\Service;

Use \Solcre\lmsuy\Entity\BuyinSessionEntity;
Use Doctrine\ORM\EntityManager;

class BuyinSessionService extends BaseService {

	public function __construct(EntityManager $em)
	{
		parent::__construct($em);
	}

	public function fetchAllBuyins($sessionId)
    {
        return $this->repository->fetchAll($sessionId);
    }

	public function add($data, $strategies = null)
	{
		$buyin = new BuyinSessionEntity();
		$buyin->setHour($data['hour']);
		$buyin->setAmountCash($data['amountCash']);
		$buyin->setAmountCredit(['setAmountCredit']);
		$buyin->setSessionUserId($data['IdUserSession']);
		$buyin->setIsApproved($data['approved']);

		$this->EntityManager->persist($buyin);
		$this->EntityManager->flush($buyin);
	}

	public function update($data, $strategies = null)
	{
		$buyin = parent::fetch($data['id']);
		$buyin->setHour($data['hour']);
		$buyin->setAmountCash($data['amountCash']);
		$buyin->setAmountCredit(['setAmountCredit']);

		$this->EntityManager->persist($buyin);
		$this->EntityManager->flush($buyin);
	}

	public function delete($id, $entityObj = null)
	{	
		$buyin = $this->entityManager->getReference('Solcre\lmsuy\Entity\BuyinSessionEntity', $id);

		$this->entityManager->remove($buyin);
		$this->entityManager->flush();
	}

	/*
	public function findOne($id)
	{
		$buyin = $this->connection->getDatosSessionBuyinById($id);
		$idSession = $this->connection->getIdSessionbyIdUserSession($buyin->session_user_id);
		$buyinObject = new BuyinSession($buyin->id, $idSession, $buyin->session_user_id, $buyin->amount_of_cash_money, $buyin->amount_of_credit_money, $buyin->currency_id, $buyin->created_at, $buyin->approved);
		$this->findEntities($buyinObject);
		return $buyinObject;
	}

	public function find($idSession)
	{

		$datosBuyins = $this->connection->getDatosSessionBuyins($idSession);

		$buyins = array();

		foreach ($datosBuyins as $buyin) 
		{
			$buyinObject = new BuyinSession($buyin->id, $idSession, $buyin->session_user_id, $buyin->amount_of_cash_money, $buyin->amount_of_credit_money, $buyin->currency_id, $buyin->created_at, $buyin->approved);

			$this->findEntities($buyinObject);

			$buyins[] = $buyinObject; 
		}

		return $buyins;
	}

	private function findEntities(BuyinSession $buyinSession) {
		$idSession = $buyinSession->getIdSession();
		$session = $this->sessionService->findOne($idSession);
		$buyinSession->setSession($session);
		$sessionUserId = $buyinSession->getSessionUserId();
		$userSession = $this->userSessionService->findOne($sessionUserId);
		$userSession->setSession($session);
		$buyinSession->setUserSession($userSession);

	}
	*/
}