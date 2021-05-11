<?php
namespace App\src;

class Password {
	public static function password_Criptografar($password) {
		$options = ['cost' => 12];
		return password_hash($password, PASSWORD_DEFAULT, $options);
	}

	public static function verify($password, $hash) {
		return password_verify($password, $hash);
	}
}