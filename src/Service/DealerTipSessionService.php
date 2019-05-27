<?php
Namespace Solcre\lmsuy\Service;

Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\DealerTipSession;

class DealerTipSessionService {

	protected $connection;

	public function __construct(ConnectLmsuy_db $connection)
	{
		$this->connection = $connection;
	}

	public function add(DealerTipSession $dealerTip)
	{
		$this->connection->insertDealerTip($dealerTip->getHour(), $dealerTip->getDealerTip(), $dealerTip->getIdSession());
	}

	public function update(DealerTipSession $dealerTip)
	{
		$this->connection-> updateDealerTip($dealerTip->getIdSession(), $dealerTip->getHour(), $dealerTip->getDealerTip(), $dealerTip->getId());
	}

	public function delete(DealerTipSession $dealerTip)
	{	
		$this->connection->deleteDealerTip($dealerTip->getId());
	}

	public function findOne($id)
	{
		$dealerTip = $this->connection->getDatosSessionDealerTipById($id);
		$dealerTipObject = new DealerTipSession($dealerTip->id, $dealerTip->session_id, $dealerTip->created_at, $dealerTip->dealer_tip);
		return $dealerTipObject;
	}

	public function find($idSession)
	{
		$datosDealerTips = $this->connection->getDatosSessionDealerTips($idSession);
		$users = array();//

		foreach ($datosDealerTips as $dealerTip) 
		{
			$dealerTipObject = new DealerTipSession($dealerTip->id, $dealerTip->session_id, $dealerTip->created_at, $dealerTip->dealer_tip);
			
			$dealerTips[] = $dealerTipObject; 
		}
		return $dealerTips;
	}

}