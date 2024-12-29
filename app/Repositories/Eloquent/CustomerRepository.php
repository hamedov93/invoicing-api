<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Models\Customer;

/**
 * Customer Repository
 */
class CustomerRepository extends EloquentRepository implements CustomerRepositoryInterface
{
	/**
	 * Construct an instance of the repo
	 * 
	 * @param \App\Models\Customer $model
	 */
	public function __construct(Customer $model)
	{
		parent::__construct($model);
	}
}
