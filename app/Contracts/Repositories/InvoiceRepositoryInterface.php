<?php

namespace App\Contracts\Repositories;

/**
 * Invoice Repository Interface
 */
interface InvoiceRepositoryInterface
{
	public function checkOverlappingInvoices(int $customerId, string $start, string $end): bool;
}
