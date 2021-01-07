<?php

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
		file_put_contents(__DIR__."/../AudioMessages/".md5($this->msg).".mp3", file_get_contents($server_output["URL"]));

		curl_close ($ch);

	}

	public function WavFileName() {
		$filename = __DIR__."/../AudioMessages/".md5($this->msg).".mp3";

		if (!file_exists($filename)) $this->convertTextToMP3(); 

		return $filename;
	}
}





