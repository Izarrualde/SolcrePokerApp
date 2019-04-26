<?php
include "../../MySQL/Connect.php";
include "../../MySQL/ConnectAppPoker.php";
Use \Solcre\pokerApp\MySQL\Connect;
Use \Solcre\pokerApp\MySQL\ConnectAppPoker;


$session = new ConnectAppPoker;
if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idT"]))
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}

$datos = $session->getDatosSessionServiceTipById($_GET["idT"]);

if (sizeof($datos)==0)
{
	die("error 404");
}

$session->deleteServiceTip();

//header("Location: ultrapro.php?var1=".$_GET['var1']."&var2=".$_GET['var2']."&var3=".$_GET['var3']);
header("Location: ../tips.php?m=".'1'."&id=".$_GET['id']); // &id=$_GET['id']");
?>