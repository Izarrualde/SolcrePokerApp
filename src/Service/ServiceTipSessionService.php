<?php
Namespace Solcre\lmsuy\Service;

Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\ServiceTipSession;

class ServiceTipSessionService {

	protected $connection;

	public function __construct(ConnectLmsuy_db $connection)
	{
		$this->connection = $connection;
	}

	public function add(ServiceTipSession $serviceTip)
	{
		$this->connection->insertServiceTip($serviceTip->getHour(), $serviceTip->getServiceTip(), $serviceTip->getIdSession());
	}

	public function update(ServiceTipSession $serviceTip)
	{
		$this->connection-> updateServiceTip($serviceTip->getIdSession(), $serviceTip->getHour(), $serviceTip->getServiceTip(), $serviceTip->getId());
	}

	public function delete(serviceTipSession $serviceTip)
	{	
		$this->connection->deleteServiceTip($serviceTip->getId());
	}

	public function findOne($id)
	{
		$serviceTip = $this->connection->getDatosSessionServiceTipById($id);
		$serviceTipObject = new ServiceTipSession($serviceTip->id, $serviceTip->session_id, $serviceTip->created_at, $serviceTip->service_tip);
		return $serviceTipObject;
	}

	public function find($idSession)
	{
		$datosServiceTips = $this->connection->getDatosSessionServiceTips($idSession);
		$users = array();//

		foreach ($datosServiceTips as $serviceTip) 
		{
			$serviceTipObject = new ServiceTipSession($serviceTip->id, $serviceTip->session_id, $serviceTip->created_at, $serviceTip->service_tip);
			
			$serviceTips[] = $serviceTipObject; 
		}
		return $serviceTips;
	}

}