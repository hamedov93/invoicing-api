<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;

/**
 * User Repository
 */
class UserRepository extends EloquentRepository implements UserRepositoryInterface
{
	/**
	 * Construct an instance of the repo
	 * 
	 * @param \App\Models\User $model
	 */
	public function __construct(User $model)
	{
		parent::__construct($model);
	}

	/**
	 * Get user by email
	 * 
	 * @param  string $email
	 * @return \App\Models\User|null
	 */
	public function getByEmail(string $email) : ?User
	{
		return $this->model->where('email', $email)->first();
	}
}
