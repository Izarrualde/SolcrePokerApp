<?php
Namespace Solcre\PokerApp\MySQL;

use Solcre\PokerApp\MySQL\Connect;

class ConnectAppPoker extends Connect
{
	private $db;

	public function __construct()
	{
		$this->db=parent::connection();
		parent::setNames();
	}

	public function getDatosUsers()
	{
		$sql="SELECT id, idUser, approved, accumulatedPoints, cashout, DATE_FORMAT(start, '%d-%m-%Y %H:%i') as start, DATE_FORMAT(end, '%d-%m-%Y %H:%i') as end FROM users";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}

	public function getDatosServiceTipSession()
	{
		$sql="SELECT id, idSession, DATE_FORMAT(hour, '%d-%m-%Y %H:%i') as hour, servicetip  FROM servicetipsession";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}


	public function getDatosDealerTipSession()
	{
		$sql="SELECT id, idSession, DATE_FORMAT(hour, '%d-%m-%Y %H:%i') as hour, dealerTip FROM dealertipsession";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}

	public function getDatosComissionSession()
	{
		$sql="SELECT id, idSession, DATE_FORMAT(hour, '%d-%m-%Y %H:%i') as hour, comission FROM comissionsession";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}

	public function getDatosBuyinSession()
	{
		$sql="SELECT id, idSession, idPlayer, amountCash, amountCredit, currency, DATE_FORMAT(hour, '%d-%m-%Y %H:%i') as hour, approved FROM buyinsession";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}


}

?>