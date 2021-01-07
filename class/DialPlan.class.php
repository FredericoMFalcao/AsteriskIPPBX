<?php


/***********************
*	 2. SCHEMA
*
***********************/
class DialPlan {

	private $children;

	/* 1. Public setters */
	public function addDialGroup(DialGroup $dg)       { $this->children[] = $dg;    return $this;}

	/* 10. VOICE MESSAGES */
	public function toSelectChildrenItemsAudioMessage() {
		$output = "";
		foreach($this->children as $i => $child) 
			$output .= $child->toSelectThisItemMessage($i+1).".\n";
		return (new AudioMessage($output))->WavFileName();
	}

	/* 20. Write ASTERISK script */
	public function printAsteriskScript() {
		$output = "";
		// 1. When the user dials this DialGroup's exact number
		$output .= "exten=>0,1,Background(".$this->toSelectChildrenItemsAudioMessage().")\n\tsame => n, WaitExten(15)\n";

		foreach($this->children as $index => $child)
			$output .= $child->printAsteriskScript($index+1) ."\n\n\n";
		return $output;
	}
}

class DialGroup {

	private $name;
	private $pronoun;

	private $children;

	/* 0. CONSTRUCTOR */
	public function __construct(string $name, string $pronoun) { $this->name = $name; $this->pronoun = $pronoun; }
	
	/* 1. Public setters */
	public function setVerbalName(string $name, string $pronoun) { $this->name = $name; $this->pronoun = $pronoun; return $this; }
	public function addPhone(Phone $phone)                { $this->children[] = $phone; return $this;}
	public function addRestEndpoint(RestEndpoint $ep)     { $this->children[] = $ep;    return $this;}
	public function addDialGroup(DialGroup $dg)           { $this->children[] = $dg;    return $this;}
	public function addDLNA_AVControl(DLNA_AVControl $da) { $this->children[] = $da;    return $this;}

	


	/* 10. VOICE MESSAGES */
	public function toSelectThisItemMessage(int $index) {
		return "Para {$this->pronoun} {$this->name} marque $index.";
	}
	public function toSelectChildrenItemsAudioMessage() {
		$output = "";
		foreach($this->children as $i => $child) 
			$output .= $child->toSelectThisItemMessage($i+1).".\n";
		return (new AudioMessage($output))->WavFileName();
	}

	/* 20. ASTERISK */
	public function printAsteriskScript(int $dialGroupIndexNumber) {
		$output = "";
		// 1. When the user dials this DialGroup's exact number
		$output .= "exten=>$dialGroupIndexNumber,1,Background(".$this->toSelectChildrenItemsAudioMessage().")\n\tsame => n, WaitExten(15)\n";

		// 2. When the user dials one of this DialGroup's Children
		foreach($this->children as $index => $child)
			$output .=  $child->printAsteriskScript($dialGroupIndexNumber.($index+1));

		return $output;
	}

}

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
		foreach($this->children as $i => $child) 
			$output .= $child->toSelectThisItemMessage($i+1).".\n";
		return (new AudioMessage($output))->WavFileName();
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

class AudioMessage {

	private $msg; 

	public function __construct(string $msg) { $this->msg = $msg; }

	public function convertTextToMP3() {
		if (!$this->msg) throw new Exception("Trying to generate an AudioMessage with an empty message.");
	
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,"https://ttsmp3.com/makemp3_new.php");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 
			  http_build_query([
				"msg"    => $this->msg,
				"lang"   => "Ines",
				"source" => "ttsmp3"
			  ]
			)
		);

		// Receive server response ... 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$server_output = json_decode(curl_exec($ch),1);

		// Save file from URL to abc.mp3
		file_put_contents(md5($this->msg).".mp3", file_get_contents($server_output["URL"]));

		curl_close ($ch);

	}

	public function WavFileName() {
		$filename = md5($this->msg).".mp3";

		if (file_exists("$filename")) return $filename;
		$this->convertTextToMP3();
		return $filename;
	}
}
