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

	public function getInvoiceDetails(int $id): Invoice
	{
		return $this->model->where('id', $id)
			->with(['lineItems', 'details', 'details.user'])
			->firstOrFail();
	}

	public function checkOverlappingInvoices(int $customerId, string $start, string $end): bool
	{
		return $this->model->where('customer_id', $customerId)
			->where(function($query) use ($start, $end) {
				$query->whereBetween('start_date', [$start, $end])
					->orWhereBetween('end_date', [$start, $end]);
			})
			->exists();
	}
}
