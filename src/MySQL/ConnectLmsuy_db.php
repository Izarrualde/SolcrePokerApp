<?php
Namespace Solcre\lmsuy\MySQL;

use Solcre\lmsuy\MySQL\Connect;

class ConnectLmsuy_db extends Connect
{
	private $db;

	public function __construct()
	{
		$this->db=parent::connection();
		parent::setNames();
	}

	public function getDatosSessionsUsers($idSession)
	{
		$sql="SELECT id, DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as created_at, points, cashout, DATE_FORMAT(start_at, '%d-%m-%Y %H:%i') as start_at,  DATE_FORMAT(end_at, '%d-%m-%Y %H:%i') as end_at, is_approved, session_id, user_id FROM sessions_users WHERE session_id='$idSession'";

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
		$sql="SELECT id, DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as created_at, points, cashout, DATE_FORMAT(start_at, '%d-%m-%Y %H:%i') as start_at,  DATE_FORMAT(end_at, '%d-%m-%Y %H:%i') as end_at, is_approved, session_id, user_id FROM sessions_users WHERE id='$id'";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}

	public function getDatosSessionServiceTips($idSession)
	{
		$sql="SELECT id,DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as created_at, service_tip, session_id  FROM session_service_tips WHERE session_id='$idSession'"; 
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
		$sql="SELECT id, DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as created_at, service_tip, session_id  FROM session_service_tips WHERE id='".$id."'";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}

	public function getDatosSessionDealerTips($idSession)
	{
		$sql="SELECT id,DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as created_at, dealer_tip, session_id  FROM session_dealer_tips WHERE session_id='$idSession'"; 
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
		$sql="SELECT id, DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as created_at, dealer_tip, session_id  FROM session_dealer_tips WHERE id='".$id."'";
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


	public function getDatosSessionComissions($idSession)
	{
		$sql="SELECT id, DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as created_at, comission, session_id FROM session_comissions WHERE session_id='$idSession'"; 
		$datos = $this->db->query($sql);
		$arreglo=array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}

	public function getDatosSessionComissionById($id)
	{
		$sql="SELECT id, DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as created_at, comission, session_id  FROM session_comissions WHERE id='".$id."'";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}

	public function getDatosSessionBuyins($idSession)
	{
		$sql="SELECT session_buyins.id, DATE_FORMAT(session_buyins.created_at, '%d-%m-%Y %H:%i') as created_at, session_buyins.amount_of_cash_money, session_buyins.amount_of_credit_money, session_buyins.session_user_id, session_buyins.approved, session_buyins.currency_id FROM session_buyins INNER JOIN sessions_users ON session_buyins.session_user_id=sessions_users.id  WHERE sessions_users.session_id='$idSession'";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}

	public function getDatosSessionBuyinById($idBuyin)
	{
	/*	$sql="SELECT session_buyins.id, DATE_FORMAT(session_buyins.created_at, '%d-%m-%Y %H:%i') as created_at, session_buyins.amount_of_cash_money, session_buyins.amount_of_credit_money, session_buyins.session_user_id, session_buyins.approved, session_buyins.currency_id FROM session_buyins INNER JOIN sessions_users ON session_buyins.session_user_id=sessions_users.id  WHERE sessions_users.session_id='$idSession'";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;*/

		$sql="SELECT id, DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as created_at, amount_of_cash_money, amount_of_credit_money, session_user_id, approved, currency_id FROM session_buyins WHERE id='$idBuyin'";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}

	public function getIdUserByUserSessionId($userSessionId)
	{
		$sql="SELECT user_id FROM sessions_users WHERE id='$userSessionId'";

		$datos = $this->db->query($sql);
		$reg=$datos->fetch_object();
		if ($reg) {
			return $reg->user_id;
		} 
		else {
			return null;
		}



	}

	public function getHourFirstBuyin($idUserSession)
	{
		$sql="SELECT MIN(created_at) as start FROM session_buyins WHERE session_user_id='$idUserSession'";

		$datos = $this->db->query($sql);

		$reg = $datos->fetch_object();
		return $reg->start;
	}

	public function getDatosSessions()
	{
		$sql="SELECT id, created_at, title, description, count_of_seats, start_at, real_start_at, end_at FROM sessions";
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}

	public function getDatosSessionById($id)
	{
		$sql="SELECT id, created_at, title, description, count_of_seats, start_at, real_start_at, end_at FROM sessions WHERE id='$id'";
		$datos = $this->db->query($sql);
		$reg=$datos->fetch_object();
		return $reg;
	}

	public function getDatosUsers()
	{
		$sql="SELECT id, created_date, username, password, name, last_name, email, cashin, points, hours, sessions, results, multiplier, is_active, avatar_hashed_filename, avatar_visible_filename  FROM users";
		
		$datos = $this->db->query($sql);
		$arreglo = array();
		while ($reg=$datos->fetch_object())
		{
			$arreglo[]=$reg;
		}
		return $arreglo;
	}


	public function getIdUserbyUsername($username)
	{
		$sql="SELECT id FROM users WHERE username='$username'";
		$datos = $this->db->query($sql);
		if ($reg=$datos->fetch_object())
		{
			return $reg->id;
		}else
		{
			return null;
		}
	}

	/*


*/
	public function getDatosUserById($id)
	{
		$sql="SELECT * FROM users WHERE id='$id'";
		$datos = $this->db->query($sql);
		$reg=$datos->fetch_object();
		return $reg;
	}
/*
	public function getLastnameUserById($id)
	{
		$sql="SELECT lastname FROM users WHERE id='$id'";
		$datos = $this->db->query($sql);
		$reg=$datos->fetch_object();
		return $reg;
	}
*/
	public function insertDealerTip($hour, $dealerTip, $idSession)
	{
		$sql="INSERT into session_dealer_tips VALUES (null, '$hour', '$dealerTip', '$idSession')"; 
		$this->db->query($sql);
	}

	public function insertServiceTip($hour, $serviceTip, $idSession)
	{
		$sql="INSERT into session_service_tips VALUES (null, '$hour', '$serviceTip', '$idSession')"; 
		$this->db->query($sql);
	}

	public function insertComission($hour, $comission, $idSession)
	{
		$sql="INSERT into session_comissions VALUES (null, '$hour', '$comission', '$idSession')"; 
		$this->db->query($sql);
	}


	public function insertBuyin($hour, $amountCash, $amountCredit, $idSessionUser, $approved, $currency)
	{
		/*$sql="SELECT count(*) from session_buyins WHERE session_user_id='$IdSessionUser'";
		if ($this->db->query($sql)==0)
		{
			$sql=
		}*/
		$sql="UPDATE sessions_users SET start_at='$hour' WHERE id='$idSessionUser' AND start_at IS NULL";

		$this->db->query($sql);
		//$idPlayer = $this->getIdUserbyNickname($_POST['nickname']);
		$sql= "INSERT into session_buyins VALUES (NULL, '$hour', '$amountCash', '$amountCredit', '$approved', '$idSessionUser', '$currency')";

		$this->db->query($sql);
	}

//INSERT INTO `session_buyins` (`id`, `created_at`, `amount_of_cash_money`, `amount_of_credit_money`, `approved`, `session_user_id`, `currency_id`) VALUES (NULL, CURRENT_TIMESTAMP, '707', '0', '1', '48', '2');

//INSERT into session_buyins VALUES (NULL, '2019-05-07T13:35', '66', '0', '48', '1', 'USD')


	//INSERT INTO `buyinsession` (`id`, `idSession`, `idPlayer`, `amountCash`, `amountCredit`, `currency`, `hour`, `approved`) VALUES (NULL, '1', 'uo', '12', '12', 'usdf', '2019-04-11 00:00:00', '1');

	public function insertUserInSession($created_at, $accumulatedPoints, $cashout, $start_at, $end_at, $is_approved, $idSession)
	{
		$users_session = $this->getDatosSessionsUsers($idSession);
		$idUser = $_POST['user_id'];
		$mensaje ='';
		
		foreach ($users_session as $user) 
		{
			if ($user->user_id==$idUser)
			{
				echo "<br>";
				echo " el jugador ya esta en la tabla";
				echo "<br>";
				if ($user->end_at != null)				
				{
					$mensaje = "";					
				}else
				{
					$mensaje = "El usuario ya habia sido agregado a esta sesión";
					break;	
				}
			}
		}


		if ($mensaje=='')
		{

			$sql="INSERT into sessions_users VALUES (null, '$created_at', '$accumulatedPoints', '$cashout',".(!empty($start_at)?$start_at:'null').", ".(!empty($end_at)?$end_at:'null').", '$is_approved', '$idSession', '$idUser')"; 

			$mensaje = "El usuario se ingresó exitosamente";
			$this->db->query($sql);			
		}
		return $mensaje;
	}

	public function insertSession($date, $title, $description, $seats, $startTime, $startTimeReal, $endTime)
	{
		$sql="INSERT into sessions VALUES (null, '$date', '$title', '$description', '$seats', '$startTime', ".(!empty($startTimeRea)?$startTimeReal:'null').", ".(!empty($endTime)?$endTime:'null').")"; 
		$this->db->query($sql);
	}

	public function addUser($created_at, $lastname, $name, $username, $mobile, $email, $password, $multiplier, $active, $hours, $points, $results, $cashin)
	{
		$users = $this->getDatosUsers();
		$mensaje = '';
		foreach ($users as $user) 
		{
			if ($user->username==$_POST['username'])
			{
				$mensaje = "El usuario ya estaba agregado";
				break;			
			}
		}
		if ($mensaje=='')
		{
			$sql="INSERT into users VALUES (null, '$created_at', '$username', '$password', '$name', '$lastname', '$email', '$cashin', '$points', '$hours', '1', '$results', '$multiplier', '$active', null, null)"; 
			$this->db->query($sql);
			echo "<br>";
			echo $hours; echo "<br>";
			$mensaje = "El usuario se ingreso exitosamente";		
		}
		return $mensaje;
	}

	public function updateComission($idSession, $hour, $comission, $id)
	{
		$sql= "UPDATE session_comissions SET session_id='$idSession', created_at='$hour', comission='$comission' WHERE id='$id'";
		$this->db->query($sql);
	}

	public function updateBuyin($amountCash, $amountCredit, $currency, $hour, $approved, $id)
	{
		$sql= "UPDATE session_buyins SET amount_of_cash_money='$amountCash', amount_of_credit_money='$amountCredit', currency_id='$currency', created_at='$hour', approved='$approved' WHERE id='$id'";

		$this->db->query($sql);
	}

	public function updateUserSession($accumulatedPoints, $cashout, $startTime, $endTime, $isApproved, $idSession, $idUser, $idUserSession)
	{
		echo empty($endTime)?"si":"no";
		$sql= "UPDATE sessions_users SET is_approved='$isApproved', points='$accumulatedPoints', cashout='$cashout', start_at='$startTime', end_at=".(!empty($endTime)?$endTime:'null')." WHERE id='$idUserSession'";
		echo "<br>";
		echo $sql;
		//".(!empty($end_at)?$end_at:'null')."

		$this->db->query($sql);
	}

	public function closeUserSession($idUserSession, $idUser, $cashout, $startTime, $endTime)
	{
		$sql= "UPDATE sessions_users SET end_at='$endTime', cashout=$cashout WHERE id='$idUserSession'";
		$this->db->query($sql);
		$date1=date_create($endTime);
		$date2=date_create($startTime);  
		//$diff=date_diff($date1, $date2)->format('%h:%i');
		
		$minutes=date_diff($date1, $date2)->format('%i');
		$roundedMinutes=floor((($minutes/60)/.25))*.25;
		$hours=date_diff($date1, $date2)->format('%h') + $roundedMinutes;

		$sql="UPDATE users SET hours=hours+".$hours." WHERE id='$idUser'";
		$this->db->query($sql);
	}

	public function updateDealerTip($idSession, $hour, $dealerTip, $id)
	{
		$sql= "UPDATE session_dealer_tips SET session_id='$idSession', created_at='$hour', dealer_tip='$dealerTip' WHERE id='$id'";
		$this->db->query($sql);
	}

	public function updateServiceTip($idSession, $hour, $serviceTip, $id)
	{
		$sql= "UPDATE session_service_tips SET session_id='$idSession', created_at='$hour', service_tip='$serviceTip' WHERE id='$id'";
		$this->db->query($sql);
	}

	public function updateSession($date, $title, $description, $seats, $startTimeReal, $endTime, $id)
	{
		$sql= "UPDATE sessions SET created_at='$date', title='$title', description='$description', count_of_seats='$seats', real_start_at='$startTimeReal', end_at='$endTime' WHERE id='$id'";
		$this->db->query($sql);
	}

		public function updateUser($name, $lastname, $username, $email, $id)
	{
		var_dump($_POST);
		$sql= "UPDATE users SET name='$name', last_name='$lastname', username='$username', email='$email' WHERE id='$id'";
		//".(!empty($end_at)?$end_at:'null')."
		echo "<br>";
		print $sql;

		$this->db->query($sql);
	}

	public function deleteComission($idComission)
	{
		$sql = "DELETE from session_comissions WHERE id='$idComission'";
		$this->db->query($sql);
	}

	public function deleteSession($id)
	{
		$deleteComissionsSession = "DELETE from session_comissions WHERE session_id='$id'";
		$this->db->query($deleteComissionsSession);

		$deleteBuyinsSession = "DELETE from session_buyins WHERE session_user_id IN (SELECT id FROM session_users WHERE session_id='$id')'";
		$this->db->query($deleteBuyinsSession);
		
		//$deleteBuyinsSession = "SELECT session_buyins.* FROM session_buyins INNER JOIN sessions ON session_buyins.session_user_id=sessions.session_user_id  WHERE sessions.id='$id'";

		$deleteDealerTipsSession = "DELETE from session_dealer_tips WHERE session_id='$id'";	
		$this->db->query($deleteDealerTipsSession);	
		$deleteServiceTipsSession = "DELETE from session_service_tips WHERE session_id='$id'";
		$this->db->query($deleteServiceTipsSession);	
		$deleteUsersSession = "DELETE from sessions_users WHERE session_id='$id'";
		$this->db->query($deleteUsersSession);	
		$sql = "DELETE from sessions WHERE id='$id'";
		$this->db->query($sql);
	}


	public function deleteBuyin($idBuyin)
	{
		$sql = "DELETE from session_buyins WHERE id='$idBuyin'";
		$this->db->query($sql);
	}


	public function deleteDealerTip($idDealerTip)
	{
		$sql = "DELETE from session_dealer_tips WHERE id='$idDealerTip'";
		$this->db->query($sql);
	}

		public function deleteServiceTip($idServiceTip)
	{
		$sql = "DELETE from session_service_tips WHERE id='$idServiceTip'";
		$this->db->query($sql);
	}

		public function deleteUser($idUserSession)
	{
		$sql = "DELETE from sessions_users WHERE id='$idUserSession'";
		$this->db->query($sql);
	}

		public function deletePlayer($idPlayer)
	{
		$sql = "DELETE from users WHERE id='$idPlayer'";
		$this->db->query($sql);
	}

}

?>