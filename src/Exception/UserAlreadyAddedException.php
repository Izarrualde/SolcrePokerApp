<?php
Namespace Solcre\lmsuy\Exception;

class UserAlreadyAddedException extends \Exception {

	public function __construct() {
		parent::__construct("El usuario ya esta agregado.");
	}

}

?>