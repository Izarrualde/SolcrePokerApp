<?php
include "../../MySQL/Connect.php";
include "../../MySQL/ConnectLmsuy_db.php";
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;


echo "<br>";
var_dump($_GET);
echo "<br>";

$connection = new ConnectLmsuy_db;
if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idT"]))
{
	die("error 404"); 
}

$datos = $connection->getDatosSessionDealerTipById($_GET["idT"]);

echo "datos es igual a getDatosSessionDealerTipById";
echo "<br>";
var_dump($datos);

if (sizeof($datos)==0)
{
	die("error 404");
}

echo "procedo a eliminar el tip";
echo "<br>";
$connection->deleteDealerTip($_GET["idT"]);
echo "ya elimine el tip";

//header("Location: ultrapro.php?var1=".$_GET['var1']."&var2=".$_GET['var2']."&var3=".$_GET['var3']);
header("Location: ../tips.php?m=".'1'."&id=".$_GET['id']); // &id=$_GET['id']");
?>