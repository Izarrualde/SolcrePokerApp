<?php
Namespace Solcre\lmsuy\Exception;

Class PlayerNotFoundException extends \Exception {
	public function __construct(){
		parent::__construct("Jugador no encontrado.");
		
	}
}
?>