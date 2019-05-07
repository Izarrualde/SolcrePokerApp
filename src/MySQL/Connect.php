<?php
Namespace Solcre\lmsuy\MySQL;

abstract class Connect
{
	private $mysqli;

	public function connection()
	{
		$this->mysqli = new \mysqli("localhost", "root", "", "lmsuy_db");
		return $this->mysqli;
	}

	public function SetNames()
	{
		return $this->mysqli->query("SET NAMES 'utf8'");
	}

}

?>