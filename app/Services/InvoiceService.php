<?php

use App\Contracts\Repositories\InvoiceRepositoryInterface;

class InvoiceService {

	private $invoiceRepository;

	public function __construct(InvoiceRepositoryInterface $invoiceRepository)
	{
		$this->invoiceRepository = $invoiceRepository;
	}

	public function create()
	{

	}
}
