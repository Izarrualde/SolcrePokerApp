<?php
include "../../MySQL/Connect.php";
include "../../MySQL/ConnectAppPoker.php";
Use \Solcre\pokerApp\MySQL\Connect;
Use \Solcre\pokerApp\MySQL\ConnectAppPoker;

echo $_GET['id'];
var_dump($_GET);

echo "<br>";

echo "<br>";
$session = new ConnectAppPoker;
if (!isset($_GET["id"]) or !is_numeric($_GET["id"]))	
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}

/*$datos = $session->getDatosSessionComissionById($_GET["idC"]);
//$datos1 = $session->getDatosSessionComissions();
if (sizeof($datos)==0)
{
	die("error 404");
}*/

$session->deleteSession();
header("Location: ../../../index.php?m=1");
?>