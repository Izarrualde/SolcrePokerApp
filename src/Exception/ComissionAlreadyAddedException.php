<?php
Namespace Solcre\lmsuy\Exception;

class ComissionAlreadyAddedException extends \Exception {

	public function __construct() {
		parent::__construct("La comision ya esta agregada.");
	}

}

?>