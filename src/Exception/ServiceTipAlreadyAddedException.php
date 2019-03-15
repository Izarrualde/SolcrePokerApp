<?php
Namespace Solcre\PokerApp\Exception;

class ServiceTipAlreadyAddedException extends \Exception {

	public function __construct() {
		parent::__construct("la propina servicio ya esta agregada.");
	}

}

?>