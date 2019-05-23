<?php
include "../vendor/autoload.php";

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\UserEntity;

date_default_timezone_set('America/Argentina/Buenos_Aires');

$connection = new ConnectLmsuy_db;
/*if (!isset($_GET["id"]) or !is_numeric($_GET["id"]))
{
	die("error 404 primero"); //porque esa id no existe, no tiene ninguna comission asociada.
}*/

$datos = $connection->getDatosUsers($_GET["id"]);
//$horaInicio = $session->getHourFirstBuyin()->start;

/*if (sizeof($datos)==0)
{
	die("error 404 segundo");
}*/

if (isset($_POST["id"]))
{
	$connection->updateUser($_POST['name'], $_POST['last_name'], $_POST['username'], $_POST['email'], $_POST['id']);
	$message = 'Usuario actualizado exitosamente';
	$template = 'viewUsers.html.twig';	
    $datosUI['breadcrumb'] = 'Usuarios';
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
}
else
{
	// datos para autocomplete
	$template = 'editPlayer.html.twig';
	$datosUI['breadcrumb'] = 'Editar usuario';

	$user = $connection->getDatosUserById($_GET['id']);

	$userObject = new UserEntity($user->id, $user->password, null, $user->email, $user->last_name, $user->name,  $user->username, $user->multiplier, $user->is_active, $user->hours, $user->points, $user->results, $user->cashin);

	$user = [
		'id' => $userObject->getId(),
		'password' => $userObject->getPassword(),
		'email' => $userObject->getEmail(),
		'name' => $userObject->getName(),
		'lastname' => $userObject->getLastname(),
		'username' => $userObject->getUsername(),
		'isActive' => $userObject->getIsActive(),
		'hours' => $userObject->getHours(),
		'points' => $userObject->getPoints(),
		'results' => $userObject->getResults(),
		'cashin' => $userObject->getCashin()
	];

	$datosUI['user'] = $user;
	var_dump($datosUI);

}


// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render($template, $datosUI);

?>