<?php
Namespace Solcre\PokerApp\Exception;

class PropinaDealerAlreadyAddedException extends \Exception {

	public function __construct() {
		parent::__construct("la propina dealer ya esta agregada.");
	}

}

?>