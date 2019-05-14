<?php
include "vendor/autoload.php";

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;

$connection = new ConnectLmsuy_db;
if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idC"]))	
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}

$datos = $connection->getDatosSessionComissionById($_GET["idC"]);
//$datos1 = $session->getDatosSessionComissions();
if (sizeof($datos)==0)
{
	die("error 404");
}

$connection->deleteComission($_GET["idC"]);
header("Location: ../comissions.php?m=".'1'."&id=".$_GET['id']);
?>