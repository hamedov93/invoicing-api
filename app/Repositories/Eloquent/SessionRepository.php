<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\SessionRepositoryInterface;
use App\Models\Session;

/**
 * Session Repository
 */
class SessionRepository extends EloquentRepository implements SessionRepositoryInterface
{
	/**
	 * Construct an instance of the repo
	 * 
	 * @param \App\Models\Session $model
	 */
	public function __construct(Session $model)
	{
		parent::__construct($model);
	}
}
