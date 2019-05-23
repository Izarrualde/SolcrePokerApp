<?php
Namespace Solcre\lmsuy\Service;

Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\BuyinSession;

class BuyinSessionService {

	protected $connection;
	protected $sessionService;
	protected $userSessionService;

	public function __construct(ConnectLmsuy_db $connection, SessionService $sessionService, userSessionService $userSessionService)
	{
		$this->connection = $connection;
		$this->sessionService = $sessionService;
		$this->userSessionService = $userSessionService;
	}

	public function add(BuyinSession $buyin)
	{
		$this->connection->insertBuyin($buyin->getHour(), $buyin->getAmountCash(), $buyin->getAmountCredit(), $buyin->getSessionUserId(), $buyin->getIsApproved(), '2');
	}

	public function update(BuyinSession $buyin)
	{
		$this->connection-> updateBuyin($buyin->getAmountCash(), $buyin->getAmountCredit(), '2', $buyin->getHour(), $buyin->getIsApproved(), $buyin->getId());
	}

	public function delete(BuyinSession $buyin)
	{	
		$this->connection->deleteBuyin($buyin->getId());
	}

	public function findOne($id)
	{
		$buyin = $this->connection->getDatosSessionBuyinById($id);
		$buyinObject = new BuyinSession($buyin->id, $_GET['id'], $buyin->session_user_id, $buyin->amount_of_cash_money, $buyin->amount_of_credit_money, $buyin->currency_id, $buyin->created_at, $buyin->approved);
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
}

