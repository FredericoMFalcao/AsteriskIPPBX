<?php

class DLNA_AVControl {

	private $name;
	private $pronoun;

	private $url;
	private $controls = [false,false,false,false,false];

	/* 0. CONSTRUCTOR */
	public function __construct(string $name, string $pronoun) { 
		$this->name = $name; $this->pronoun = $pronoun; 
	}
	/* 1. Public setters */
	public function setUrl(string $url) { $this->url = $url; return $this; }

	public function implementPrevious(bool $b = true)   { $this->controls[0] = true; }
	public function implementStop(bool $b = true)       { $this->controls[1] = true; }
	public function implementNext(bool $b = true)       { $this->controls[2] = true; }
	public function implementVolumeUp(bool $b = true)   { $this->controls[3] = true; }
	public function implementVolumeDown(bool $b = true) { $this->controls[4] = true; }

	/* 10. VOICE MESSAGES */
	public function toSelectThisItemMessage(int $index) {
		return "Para controlar {$this->pronoun} {$this->name} marque $index";
	}
	public function toSelectChildrenItemsAudioMessage() {
		$output  = "";
		if ($this->controls[0]) $output .= "Para ir para trás marque 1.\n";
		if ($this->controls[1]) $output .= "Para ir para a frente marque 2.\n";
		if ($this->controls[2]) $output .= "Para ir parar marque 3.\n";
		if ($this->controls[3]) $output .= "Para aumentar volume marque 4.\n";
		if ($this->controls[4]) $output .= "Para diminuir volume marque 5.\n";

		if(!empty($output)) 
			return (new AudioMessage($output))->WavFileName();
		else
			return "";
	}

	/* 20. ASTERISK SCRIPT */
	public function printAsteriskScript(int $phoneNumber) {
		$output = "";
		// 1. When the user dials this DialGroup's exact number
		$output .= "exten=>$phoneNumber,1,Background(".$this->toSelectChildrenItemsAudioMessage().")\n\tsame => n, WaitExten(15)\n";

		// 2. When the user dials one of this DialGroup's Children
		if ($this->controls[0]) $output .= "exten=>{$phoneNumber}1,1,System(curl ...)";
		if ($this->controls[1]) $output .= "exten=>{$phoneNumber}2,1,System(curl ...)";
		if ($this->controls[2]) $output .= "exten=>{$phoneNumber}3,1,System(curl ...)";
		if ($this->controls[3]) $output .= "exten=>{$phoneNumber}4,1,System(curl ...)";
		if ($this->controls[4]) $output .= "exten=>{$phoneNumber}5,1,System(curl ...)";

		return $output;

	}
	
}

