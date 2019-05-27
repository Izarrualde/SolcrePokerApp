<?php
include "vendor/autoload.php";

Use \Solcre\lmsuy\Service\UserService;
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;

//pendiente
date_default_timezone_set('America/Argentina/Buenos_Aires');

$datosUI = array();

if (isset($_POST['id']))
{
	$connection = new ConnectLmsuy_db;
	$userService = new UserService($connection);

	//agregar excepcion si user esta agregado
	$datosUI['message'] = $connection->addUser(date('c'), $_POST['lastname'], $_POST['firstname'], $_POST['username'], $_POST['mobile'], $_POST['email'], $_POST['password'], $_POST['multiplier'], $_POST['active'], $_POST['hours'], $_POST['points'], $_POST['results'], $_POST['cashin'], null, null);
	$template = 'viewusers.html.twig';
	$datosUI['breadcrumb'] = 'Usuarios';
	$connection = new ConnectLmsuy_db;
	$datosUsers = $connection->getDatosUsers();

	// hidrato objetos con datos de la bdd y a la vez desarrollo datosUI segun requierimientos.
	foreach ($datosUsers as $user) 
	{
		$userObject = new UserEntity($user->id, $user->password, null, $user->email, $user->last_name, $user->name,  $user->username, $user->multiplier, $user->is_active, $user->hours, $user->points, $user->results, $user->cashin);


		$users[] = [
			'id' => $userObject->getId(),
			'name' => $userObject->getName(),
			'lastname' => $userObject->getLastname(), //quisiera no tener este aca pero en el template al iterar en comission no logro acceder a session.id
			'hours' => $userObject->getHours()
		];
	}

	//$datosUI[] = ['users' => $users];

	/*$datosUI['users'] = [
			'users' => $users
	];*/

	$datosUI['users'] = $users;
}
else
{
	$template = 'adduser.html.twig';
	$datosUI['breadcrumb'] = 'Nuevo usuario';
	// BUSQUEDA DE DATOS PARA LA UI
	// no se necesitan!
}

// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

echo $twig->render($template, $datosUI);

?>