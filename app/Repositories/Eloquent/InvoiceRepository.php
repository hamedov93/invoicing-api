<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\InvoiceRepositoryInterface;
use App\Models\Invoice;

/**
 * Invoice Repository
 */
class InvoiceRepository extends EloquentRepository implements InvoiceRepositoryInterface
{
	/**
	 * Construct an instance of the repo
	 * 
	 * @param \App\Models\Invoice $model
	 */
	public function __construct(Invoice $model)
	{
		parent::__construct($model);
	}
}
