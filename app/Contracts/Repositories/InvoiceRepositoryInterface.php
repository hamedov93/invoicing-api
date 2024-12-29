<?php

namespace App\Contracts\Repositories;

use App\Models\Invoice;

/**
 * Invoice Repository Interface
 */
interface InvoiceRepositoryInterface
{
	public function getInvoiceDetails(int $id): Invoice;
	public function checkOverlappingInvoices(int $customerId, string $start, string $end): bool;
}
