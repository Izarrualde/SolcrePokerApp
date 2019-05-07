<?php
Namespace Solcre\lmsuy\Exception;

class PropinaServicioAlreadyAddedException extends \Exception {

	public function __construct() {
		parent::__construct("la propina servicio ya esta agregada.");
	}

}

?>