<?php
include "../../MySQL/Connect.php";
include "../../MySQL/ConnectAppPoker.php";
Use \Solcre\pokerApp\MySQL\Connect;
Use \Solcre\pokerApp\MySQL\ConnectAppPoker;

$session = new ConnectAppPoker;
if (!isset($_GET["id"]) or !is_numeric($_GET["id"]))
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}

$datos = $session->getDatosSessionBuyinById($_GET["id"]);
if (sizeof($datos)==0)
{
	die("error 404");
}

$session->deleteBuyin();
header("Location: ../buyins.php?m=1");
?>
