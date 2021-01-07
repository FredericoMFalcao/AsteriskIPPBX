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

