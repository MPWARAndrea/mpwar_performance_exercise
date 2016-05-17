<?php

namespace Performance\Domain;

class Author
{
	private $id;
	private $username;
	private $password;
	private $picture;

	public static function register($username, $password, $picture) {
		$author = new Author();
		$author->username 	= $username;
		$author->password 	= password_hash($password, PASSWORD_DEFAULT);
		$author->picture	= $picture;

		return $author;
	}

	public static function fromArray($authorArray) {
		$author = new Author();
		$author->id = $authorArray['id'];
		$author->username = $authorArray['username'];
		$author->password = $authorArray['password'];
		$author->password = $authorArray['picture'];

		return $author;
	}

	public function verifyPassword($plainTextPassword) {
		return password_verify($plainTextPassword, $this->password);
	}

	public function getId() {
		return $this->id;
	}

	public function getUsername() {
		return $this->username;
	}

	public function getPicture() {
		return $this->picture;
	}
}