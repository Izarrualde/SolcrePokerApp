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

	public function getDatosSessionUsers()
	{
		$sql="SELECT id, session_id, user_id, approved, accumulated_points, cashout, DATE_FORMAT(start, '%d-%m-%Y %H:%i') as start, DATE_FORMAT('end', '%d-%m-%Y %H:%i') as 'end' FROM session_users";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}


	public function getDatosSessionUsersById($id)
	{
		$sql="SELECT id, session_id, user_id, approved, accumulated_points, cashout, DATE_FORMAT(start, '%d-%m-%Y %H:%i') as start, DATE_FORMAT(end, '%d-%m-%Y %H:%i') as end FROM session_users WHERE id='".$id."'";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}

	public function getDatosSessionServiceTips()
	{
		$sql="SELECT id, session_id, DATE_FORMAT(hour, '%d-%m-%Y %H:%i') as hour, service_tip  FROM session_service_tips";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}

	public function getDatosSessionServiceTipById($id)
	{
		$sql="SELECT id, session_id, DATE_FORMAT(hour, '%d-%m-%Y %H:%i') as hour, service_tip FROM session_service_tips WHERE id='".$id."'";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}

	public function getDatosSessionDealerTips()
	{
		$sql="SELECT id, session_id, DATE_FORMAT(hour, '%d-%m-%Y %H:%i') as hour, dealer_tip FROM session_dealer_tips";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}

	public function getDatosSessionDealerTipById($id)
	{
		$sql="SELECT id, session_id, DATE_FORMAT(hour, '%d-%m-%Y %H:%i') as hour, dealer_tip FROM session_dealer_tips WHERE id='".$id."'";
		$datos = $this->db->query($sql);
		$arreglo = array();
		if (!$datos)
		{
			return $arreglo;
		}
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}


	public function getDatosSessionComissions()
	{
		$sql="SELECT id, session_id, DATE_FORMAT(hour, '%d-%m-%Y %H:%i') as hour, comission FROM session_comissions";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}

	public function getDatosSessionComissionById($id)
	{
		$sql="SELECT id, session_id, DATE_FORMAT(hour, '%d-%m-%Y %H:%i') as hour, comission FROM session_comissions WHERE id='".$id."'";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}

	public function getDatosSessionBuyins()
	{
		$sql="SELECT id, session_id, player_id, amount_cash, amount_credit, currency, DATE_FORMAT(hour, '%d-%m-%Y %H:%i') as hour, approved FROM session_buyins";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}

	public function getDatosSessionBuyinById($id)
	{
		$sql="SELECT id, session_id, player_id, amount_cash, amount_credit, currency, DATE_FORMAT(hour, '%d-%m-%Y %H:%i') as hour, approved FROM session_buyins WHERE id='".$id."'";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}

	public function getDatosSessions()
	{
		$sql="SELECT id, title, description, date, start_time, start_time_real, end_time FROM sessions";
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
		$sql="INSERT into session_dealer_tips VALUES (null, '$_POST[idSession]', '$_POST[hour]', '$_POST[dealerTip]')"; 
		$this->db->query($sql);
	}

	public function insertServiceTip()
	{
		$sql="INSERT into session_service_tips VALUES (null, '$_POST[idSession]', '$_POST[hour]', '$_POST[serviceTip]')"; 
		$this->db->query($sql);
	}

	public function insertComission()
	{
		$sql="INSERT into session_comissions VALUES (null, '$_POST[idSession]', '$_POST[hour]', '$_POST[comission]')"; 
		$this->db->query($sql);
	}


	public function insertBuyin()
	{

		$sql= "INSERT into session_buyins VALUES (NULL, '$_POST[idSession]', '$_POST[idPlayer]', '$_POST[amountCash]', '$_POST[amountCredit]', '$_POST[currency]', '$_POST[hour]', '1')";
		$this->db->query($sql);
	}

	//INSERT INTO `buyinsession` (`id`, `idSession`, `idPlayer`, `amountCash`, `amountCredit`, `currency`, `hour`, `approved`) VALUES (NULL, '1', 'uo', '12', '12', 'usdf', '2019-04-11 00:00:00', '1');

	public function insertUser()
	{
		$sql="INSERT into session_users VALUES (null, '$_POST[idUser]', '1', '$_POST[accumulatedPoints]', '$_POST[cashout]', '$_POST[start]', '$_POST[end]')"; 
		$this->db->query($sql);
	}

	public function insertSession()
	{
		$sql="INSERT into sessions VALUES (null, '$_POST[title]', '$_POST[description]', '$_POST[date]', '$_POST[seats]', '$_POST[startTime]', '$_POST[startTimeReal]', '$_POST[end]')"; 
		$this->db->query($sql);
	}

	public function updateComission()
	{
		$sql= "UPDATE session_comissions SET session_id='$_POST[IdSession]', hour='$_POST[hour]', comission='$_POST[comission]' WHERE id='$_POST[id]'";
		$this->db->query($sql);
	}

	public function updateBuyin()
	{
		$sql= "UPDATE buyinsession SET idSession='$_POST[idSession]', idPlayer='$_POST[idPlayer]', amountCash='$_POST[amountCash]', amountCredit='$_POST[amountCredit]', currency='$_POST[currency]', hour='$_POST[hour]', approved='$_POST[approved]' WHERE id='$_POST[id]'";
		$this->db->query($sql);
	}

	public function updateUser()
	{
		$sql= "UPDATE session_users SET user_id='$_POST[userId]', approved='$_POST[approved]', accumulated_points='$_POST[accumulatedPoints]', cashout='$_POST[cashout]', start='$_POST[start]', end='$_POST[end]' WHERE id='$_POST[id]'";
		$this->db->query($sql);
	}

	public function updateDealerTip()
	{
		$sql= "UPDATE session_dealer_tips SET session_id='$_POST[idSession]', hour='$_POST[hour]', dealer_tip='$_POST[dealerTip]' WHERE id='$_POST[id]'";
		$this->db->query($sql);
	}

	public function updateServiceTip()
	{
		$sql= "UPDATE session_service_tips SET session_id='$_POST[idSession]', hour='$_POST[hour]', service_tip='$_POST[serviceTip]' WHERE id='$_POST[id]'";
		$this->db->query($sql);
	}


	public function deleteComission()
	{
		$sql = "DELETE from session_comissions WHERE id='$_GET[id]'";
		$this->db->query($sql);
	}

	public function deleteBuyin()
	{
		$sql = "DELETE from session_buyins WHERE id='$_GET[id]'";
		$this->db->query($sql);
	}

	public function deleteDealerTip()
	{
		$sql = "DELETE from session_dealer_tips WHERE id='$_GET[id]'";
		$this->db->query($sql);
	}

		public function deleteServiceTip()
	{
		$sql = "DELETE from session_service_tips WHERE id='$_GET[id]'";
		$this->db->query($sql);
	}

		public function deleteUser()
	{
		$sql = "DELETE from ssession_users WHERE id='$_GET[id]'";
		$this->db->query($sql);
	}

}

?>