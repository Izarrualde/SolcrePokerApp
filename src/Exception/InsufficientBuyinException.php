<?php
Namespace Solcre\PokerApp\Exception;

Class InsufficientBuyinException extends \Exception {
	public function __construct(){
		paret::__construct("buyin insuficiente.");
		
	}
}