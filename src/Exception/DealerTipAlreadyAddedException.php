<?php
Namespace Solcre\PokerApp\Exception;

class DealerTipAlreadyAddedException extends \Exception {

	public function __construct() {
		parent::__construct("la propina dealer ya esta agregada.");
	}

}

?>