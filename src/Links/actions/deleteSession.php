<?php
include "../../MySQL/Connect.php";
include "../../MySQL/ConnectLmsuy_db.php";
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;

$session = new ConnectLmsuy_db;
if (!isset($_GET["id"]) or !is_numeric($_GET["id"]))	
{
	die("error 404");
}

$session->deleteSession($_GET["id"]);
header("Location: ../../../index.php?m=1");
?>