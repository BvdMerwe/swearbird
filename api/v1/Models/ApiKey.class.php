<?php 
class ApiKey {

	private $key = "cow";
	private $loc = "localhost";

	public function __construct(){

	}

	public function verifyKey($key, $origin) {
		// if key and origin link
		// return true;
		// else
		// return false;
		// echo "$key == ".$this->key." && $origin == ".$this->loc;
		if ($key == $this->key /*&& $origin == $this->loc*/) {
			return true;
		}
		return false;
	}
}

?>