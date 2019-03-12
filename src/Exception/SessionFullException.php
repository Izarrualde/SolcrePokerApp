<?php
Namespace Solcre\PokerApp\Exception;

class SessionFullException extends \Exception {

	public function __construct() {
		parent::__construct("La sesion esta llena.");
	}

}

?>