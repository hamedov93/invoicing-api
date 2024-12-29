<?php

use App\Contracts\Repositories\UserRepositoryInterface;

class UserService {

	private $userRepository;

	public function __construct(UserRepositoryInterface $userRepository)
	{
		$this->userRepository = $userRepository;
	}
}