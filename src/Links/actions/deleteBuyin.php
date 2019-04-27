<?php
include "../../MySQL/Connect.php";
include "../../MySQL/ConnectAppPoker.php";
Use \Solcre\pokerApp\MySQL\Connect;
Use \Solcre\pokerApp\MySQL\ConnectAppPoker;

echo "<br>";
var_dump($_GET);
echo "<br>";
//var_dump($datos1);
echo "<br>";


$session = new ConnectAppPoker;
if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idB"]))
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}

$datos = $session->getDatosSessionBuyinById($_GET["idB"]);
echo "<br>";
echo "get datos por id";
var_dump($datos);
echo "<br>";
if (sizeof($datos)==0)
{
	die("error 404");
}

$session->deleteBuyin();
header("Location: ../buyins.php?m=".'1'."&id=".$_GET['id']);
?>
