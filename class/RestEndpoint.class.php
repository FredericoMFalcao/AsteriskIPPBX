<?php

class RestEndpoint{
	private $name;
	private $pronoun;

	private $Url;
	private $Method;
	public function __construct(string $name, string $pronoun, string $Url, string $Method = "GET") { 
		$this->name = $name; $this->pronoun = $pronoun; 
		$this->Url = $Url;  $this->Method = $Method;
	}
	
	/* 10. VOICE MESSAGES */
	public function toSelectThisItemMessage(int $index) {
		return "Para {$this->pronoun} {$this->name} marque $index";
	}

	/* 20. ASTERISK SCRIPT */
	public function printAsteriskScript(int $phoneNumber) {
		return "exten=>$phoneNumber,1,System(curl -L '{$this->Url}')\n";
	}

}

