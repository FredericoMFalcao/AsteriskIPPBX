<?php

class DialGroup {

	private $name;
	private $pronoun;

	private $children;

	/* 0. CONSTRUCTOR */
	public function __construct(string $name, string $pronoun) { $this->name = $name; $this->pronoun = $pronoun; }
	
	/* 1. Public setters */
	public function setVerbalName(string $name, string $pronoun) { $this->name = $name; $this->pronoun = $pronoun; return $this; }
	public function addPhone(Phone $phone)                  { $this->children[] = $phone; return $this;}
	public function addRestEndpoint(RestEndpoint $ep)       { $this->children[] = $ep;    return $this;}
	public function addDialGroup(DialGroup $dg)             { $this->children[] = $dg;    return $this;}
	public function addDLNA_AVControl(DLNA_AVControl $da)   { $this->children[] = $da;    return $this;}
	public function addRoladexAudio(RoladexAudio $ra)       { $this->children[] = $ra;    return $this;}
	public function addPlayerFMPodcast(PlayerFMPodcast $pc) { $this->children[] = $pc;    return $this;}
	public function addAudioStream(AudioStream $as)         { $this->children[] = $as;    return $this;}
	


	/* 10. VOICE MESSAGES */
	public function toSelectThisItemMessage(int $index) {
		return "Para {$this->pronoun} {$this->name} marque $index.";
	}
	public function toSelectChildrenItemsAudioMessage() {
		$output = "";
		foreach($this->children as $i => $child) 
			$output .= $child->toSelectThisItemMessage($i+1).".\n";
		if (!empty($output))
			return (new AudioMessage($output))->WavFileName();
		else
			return "Empty";
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

