<?php
include "../../MySQL/Connect.php";
include "../../MySQL/ConnectAppPoker.php";
Use \Solcre\pokerApp\MySQL\Connect;
Use \Solcre\pokerApp\MySQL\ConnectAppPoker;


$session = new ConnectAppPoker;
if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idU"]))	
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}

$datos = $session->getDatosSessionUsersById($_GET["idU"]);

if (sizeof($datos)==0)
{
	die("error 404");
}

$session->deleteUser();
header("Location: ../users.php?m=".'1'."&id=".$_GET['id']);
?>