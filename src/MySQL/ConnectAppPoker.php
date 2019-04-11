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
		$sql="SELECT id, idUser, approved, accumulatedPoints, cashout, DATE_FORMAT(start, '%d-%m-%Y %H:%i') as start, DATE_FORMAT(end, '%d-%m-%Y %H:%i') as end FROM userssession";
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

	public function insertDealerTip()
	{
		$sql="INSERT into dealertipsession VALUES (null, '$_POST[idSession]', '$_POST[hora]', '$_POST[dealerTip]')"; 
		$this->db->query($sql);
	}

	public function insertServiceTip()
	{
		$sql="INSERT into servicetipsession VALUES (null, '$_POST[idSession]', '$_POST[hora]', '$_POST[servicetip]')"; 
		$this->db->query($sql);
	}

	public function insertComission()
	{
		$sql="INSERT into comissionsession VALUES (null, '$_POST[idSession]', '$_POST[hora]', '$_POST[comission]')"; 
		$this->db->query($sql);
	}


	public function insertBuyin()
	{

		$sql= "INSERT into buyinsession VALUES (NULL, '$_POST[idSession]', '$_POST[idPlayer]', '$_POST[amountCash]', '$_POST[amountCredit]', '$_POST[currency]', '$_POST[hora]', '1')";
		$this->db->query($sql);
	}

	//INSERT INTO `buyinsession` (`id`, `idSession`, `idPlayer`, `amountCash`, `amountCredit`, `currency`, `hour`, `approved`) VALUES (NULL, '1', 'uo', '12', '12', 'usdf', '2019-04-11 00:00:00', '1');

	public function insertUser()
	{
		$sql="INSERT into userssession VALUES (null, '$_POST[idUser]', '1', '$_POST[accumulatedPoints]', '$_POST[cashout]', '$_POST[start]', '$_POST[end]')"; 
		$this->db->query($sql);
	}


}

?>