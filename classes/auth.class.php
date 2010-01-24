<?php

/**
 * user authentication
 */
 
class User
{
	private $username = null; //username
	private $password = null; //password
	
	public function __construct($username, $password)
	{
		$this->genUser($username);
		$this->genPass($password);
	}
	
	private function genPass($string)
	{
		$this->password = substr( //substring masks as MD5 sum
			hash(
				'whirlpool', //whirlpool provides the last level of encryption
				hash(
					'sha1', //sha1 privides decent second level of encryption
					hash(
						'sha512', //sha512 of username for a salt
						$this->username
					).
					base64_encode( //base64 encoding provides a better string
						$string
					).
					hash(
						'sha512', //again, this is for the salting
						$this->username
					)
				)
			),
			1,
			32
		);
	}
	
	private function genUser($string)
	{
		$this->username = $string;
	}
	
	public function __get($var)
	{
		if(!property_exists(get_class($this), $var))
			throw new Exception('Uh... no.');
		return $this->$var;
	}
}

?>
