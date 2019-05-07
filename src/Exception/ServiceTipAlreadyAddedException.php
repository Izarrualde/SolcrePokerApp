<?php
Namespace Solcre\lmsuy\Exception;

class ServiceTipAlreadyAddedException extends \Exception {

	public function __construct() {
		parent::__construct("la propina servicio ya esta agregada.");
	}

}

?>