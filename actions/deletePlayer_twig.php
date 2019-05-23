<?php
include "../vendor/autoload.php";

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\UserEntity;


$connection = new ConnectLmsuy_db;
/*if (!isset($_GET["id"]) or !is_numeric($_GET["id"]))
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}*/

/*$datos = $session->getDatosSessionUsersById($_GET["idU"]);

if (sizeof($datos)==0)
{
	die("error 404");
}*/

$connection->deletePlayer($_GET['id']);
$message = 'Usuario eliminado exitosamente';
//extraigo datos de la bdd

$datosUsers = $connection->getDatosUsers();

// hidrato objetos con datos de la bdd y a la vez desarrollo datosUI segun requierimientos.


foreach ($datosUsers as $user) 
{

	$userObject = new UserEntity($user->id, $user->password, null, $user->email, $user->last_name, $user->name,  $user->username, $user->multiplier, $user->is_active, $user->hours, $user->points, $user->results, $user->cashin);


	$users[] = [
		'id' => $userObject->getId(),
		'name' => $userObject->getName(),
		'lastname' => $userObject->getLastname(),
		'hours' => $userObject->getHours()
	];
}

//$datosUI[] = ['users' => $users];

/*$datosUI['users'] = [
		'users' => $users
];*/

$datosUI['users'] = $users;
$datosUI['breadcrumb'] = 'Usuarios';
$datosUI['message'] = $message;



$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('viewUsers.html.twig', $datosUI);

?>