<?php
include "vendor/autoload.php";

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;


$connection = new ConnectLmsuy_db;
if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idT"]))
{
	die("error 404");
}

$datos = $connection->getDatosSessionServiceTipById($_GET["idT"]);

if (sizeof($datos)==0)
{
	die("error 404");
}

$connection->deleteServiceTip($_GET["idT"]);

//header("Location: ultrapro.php?var1=".$_GET['var1']."&var2=".$_GET['var2']."&var3=".$_GET['var3']);
header("Location: ../tips.php?m=".'2'."&id=".$_GET['id']); // &id=$_GET['id']");
?>