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

$datos = $session->getDatosComissionSessionForId($_GET["id"]);
$datos1 = $session->getDatosComissionSession();
if (sizeof($datos)==0)
{
	echo "<br>";
	var_dump($datos);
	echo "<br>";
	var_dump($datos1);
	echo "<br>";
	die("error 404");
}

$session->deleteComission();
header("Location: ../comissions.php?m=1");
?>