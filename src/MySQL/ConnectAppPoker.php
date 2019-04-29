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
		$sql="SELECT id, session_id, user_id, approved, accumulated_points, cashout, DATE_FORMAT(start, '%d-%m-%Y %H:%i') as start, DATE_FORMAT('end', '%d-%m-%Y %H:%i') as end FROM session_users WHERE session_id='$_GET[id]'";
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
		$sql="SELECT id, session_id, user_id, approved, accumulated_points, cashout, DATE_FORMAT(start, '%d-%m-%Y %H:%i') as start, DATE_FORMAT(end, '%d-%m-%Y %H:%i') as end FROM session_users WHERE user_id='".$id."'";
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
		$sql="SELECT id, session_id, DATE_FORMAT(hour, '%d-%m-%Y %H:%i') as hour, service_tip  FROM session_service_tips WHERE session_id='$_GET[id]'";
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
		$sql="SELECT id, session_id, DATE_FORMAT(hour, '%d-%m-%Y %H:%i') as hour, dealer_tip FROM session_dealer_tips WHERE session_id='$_GET[id]'";
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
		$sql="SELECT id, session_id, DATE_FORMAT(hour, '%d-%m-%Y %H:%i') as hour, comission FROM session_comissions WHERE session_id='$_GET[id]'";
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
		$sql="SELECT id, session_id, player_id, amount_cash, amount_credit, currency, DATE_FORMAT(hour, '%d-%m-%Y %H:%i') as hour, approved FROM session_buyins WHERE session_id='$_GET[id]'";
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

	public function getHourFirstBuyin()
	{
		$sql="SELECT MIN(hour) as start FROM session_buyins WHERE session_id='$_GET[id]' AND player_id='$_GET[idU]'";
		$datos = $this->db->query($sql);
		$reg = $datos->fetch_object();
		return $reg;
	}

	public function getDatosSessions()
	{
		$sql="SELECT id, title, description, date, seats, start_time, start_time_real, end_time FROM sessions";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}

	public function getDatosUsers()
	{
		$sql="SELECT id, lastname, firstname, nickname, mobile, email, password, multiplier, active, hours, points, results, cashin FROM users";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}


	public function getIdUserbyNickname($nickname)
	{
		$sql="SELECT id FROM users WHERE nickname='$nickname'";
		$datos = $this->db->query($sql);
		$reg=$datos->fetch_object();
		return $reg;
	}

	public function getNicknameUserbyId($id)
	{
		$sql="SELECT nickname FROM users WHERE id='$id'";
		$datos = $this->db->query($sql);
		$reg=$datos->fetch_object();
		return $reg;
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
		echo "estoy en function insertComission";
		echo "<br>";
		var_dump($_POST);
		echo "<br>";
		$sql="INSERT into session_comissions VALUES (null, '$_POST[idSession]', '$_POST[hour]', '$_POST[comission]')"; 
		$this->db->query($sql);
	}


	public function insertBuyin()
	{
		$idPlayer = $this->getIdUserbyNickname($_POST['nickname']);
		$sql= "INSERT into session_buyins VALUES (NULL, '$_POST[idSession]', '$idPlayer->id', '$_POST[amountCash]', '$_POST[amountCredit]', '$_POST[currency]', '$_POST[hour]', '1')";
		$this->db->query($sql);
	}

	//INSERT INTO `buyinsession` (`id`, `idSession`, `idPlayer`, `amountCash`, `amountCredit`, `currency`, `hour`, `approved`) VALUES (NULL, '1', 'uo', '12', '12', 'usdf', '2019-04-11 00:00:00', '1');

	public function insertUser()
	{
		$users_session = $this->getDatosSessionUsers();
		$idUser = $this->getIdUserbyNickname($_POST['nickname']);
		$mensaje ='';
		foreach ($users_session as $user) 
		{
			if ($user->user_id==$idUser->id)
			{
				$mensaje = "El usuario ya habia sido agregado a esta sesión";
				break;			
			}
		}
		if ($mensaje=='')
		{
			$sql="INSERT into session_users VALUES (null, '$_POST[idSession]','$idUser->id', '$_POST[approved]', '$_POST[accumulatedPoints]', '$_POST[cashout]', '$_POST[start]', '$_POST[end]')"; 
			$mensaje = "El usuario se ingreso exitosamente";
			$this->db->query($sql);			
		}
		return $mensaje;
	}

	public function insertSession()
	{
		$sql="INSERT into sessions VALUES (null, '$_POST[title]', '$_POST[description]', '$_POST[date]', '$_POST[seats]', '$_POST[startTime]', '$_POST[startTimeReal]', '$_POST[end]')"; 
		$this->db->query($sql);
	}

	public function addUser()
	{
		$users = $this->getDatosUsers();
		$mensaje = '';
		foreach ($users as $user) 
		{
			if ($user->mobile==$_POST['mobile'])
			{
				$mensaje = "El usuario ya estaba agregado";
				break;			
			}
		}
		if ($mensaje=='')
		{
		$sql="INSERT into users VALUES (null, '$_POST[lastname]', '$_POST[firstname]', '$_POST[nickname]', '$_POST[mobile]', '$_POST[email]', '$_POST[password]', '$_POST[multiplier]', '$_POST[active]', '$_POST[hours]', '$_POST[points]', '$_POST[results]', '$_POST[cashin]')"; 
		$this->db->query($sql);
			$mensaje = "El usuario se ingreso exitosamente";
			$this->db->query($sql);			
		}
		return $mensaje;
	}

	public function updateComission()
	{
		$sql= "UPDATE session_comissions SET session_id='$_POST[idSession]', hour='$_POST[hour]', comission='$_POST[comission]' WHERE id='$_POST[id]'";
		$this->db->query($sql);
	}

	public function updateBuyin()
	{
		$sql= "UPDATE session_buyins SET session_id='$_POST[idSession]', player_id='$_POST[idPlayer]', amount_cash='$_POST[amountCash]', amount_credit='$_POST[amountCredit]', currency='$_POST[currency]', hour='$_POST[hour]', approved='$_POST[approved]' WHERE id='$_POST[id]'";
		$this->db->query($sql);
	}

	public function updateUser()
	{
		$sql= "UPDATE session_users SET approved='$_POST[approved]', accumulated_points='$_POST[accumulatedPoints]', cashout='$_POST[cashout]', start='$_POST[start]', end='$_POST[end]' WHERE id='$_POST[id]'";
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
		$sql = "DELETE from session_comissions WHERE id='$_GET[idC]'";
		$this->db->query($sql);
	}

	public function deleteBuyin()
	{
		$sql = "DELETE from session_buyins WHERE id='$_GET[idB]'";
		$this->db->query($sql);
	}

	public function deleteDealerTip()
	{
		$sql = "DELETE from session_dealer_tips WHERE id='$_GET[idT]'";
		$this->db->query($sql);
	}

		public function deleteServiceTip()
	{
		$sql = "DELETE from session_service_tips WHERE id='$_GET[idT]'";
		$this->db->query($sql);
	}

		public function deleteUser()
	{
		$sql = "DELETE from session_users WHERE id='$_GET[idU]'";
		$this->db->query($sql);
	}

}

?>