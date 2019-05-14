<?php
include "vendor/autoload.php";

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;


$connection = new ConnectLmsuy_db;
if (!isset($_GET["id"]) or !is_numeric($_GET["id"]))
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}

/*$datos = $session->getDatosSessionUsersById($_GET["idU"]);

if (sizeof($datos)==0)
{
	die("error 404");
}*/

$connection->deletePlayer($_GET['id']);
header("Location: ../viewUsers.php?m=".'1'."&id=".$_GET['id']);
?>