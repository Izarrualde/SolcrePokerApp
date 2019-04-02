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
		$sql="SELECT * FROM users";
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
		$sql="SELECT * FROM servicetipsession";
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
		$sql="SELECT * FROM dealertipsession";
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
		$sql="SELECT * FROM comissionsession";
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
		$sql="SELECT * FROM buyinsession";
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