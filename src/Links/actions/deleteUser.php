<?php
include "../../MySQL/Connect.php";
include "../../MySQL/ConnectLmsuy_db.php";
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;


$connection = new ConnectLmsuy_db;
if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idU"]))	
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}

/*$datos = $session->getDatosSessionUsersById($_GET["idU"]);

if (sizeof($datos)==0)
{
	die("error 404");
}*/

$connection->deleteUser($_GET['idU']);
header("Location: ../users.php?m=".'1'."&id=".$_GET['id']);
?>