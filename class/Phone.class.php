<?php

class Phone {
	private $name;
	private $pronoun;

	private $SipName;
	private $dialForXSeconds = 30;

	public function __construct(string $name, string $pronoun, string $SipName) { $this->name = $name; $this->pronoun = $pronoun; $this->SipName = $SipName;  }
	
	/* 10. VOICE MESSAGES */
	public function toSelectThisItemMessage(int $index) {
		return "Para ligar para {$this->pronoun} {$this->name} marque $index";
	}

	/* 20. ASTERISK */
	public function printAsteriskScript(int $phoneNumber) {
		return "exten=>$phoneNumber,1, Dial(PJSIP/{$this->SipName},{$this->dialForXSeconds})\n";
	}
}

