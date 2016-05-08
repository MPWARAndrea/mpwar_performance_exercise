<?php

namespace Performance\Domain\UseCase;

use Performance\Domain\Author;
use Performance\Domain\AuthorRepository;
use Performance\Domain\AwsSThreeService;

class SignUp
{
	/**
	 * @var AuthorRepository
	 */
	private $authorRepository;

	/**
	 * @var AwsSThreeService
	 */
	private $aws;

	public function __construct(AuthorRepository $authorRepository,
								AwsSThreeService $aws) {
		$this->authorRepository = $authorRepository;
		$this->aws = $aws;
		$this->aws->connectAws();
	}

	public function execute($username, $password, $profile_picture) {
		$author = Author::register($username, $password);
		$this->authorRepository->save($author);

	}
}