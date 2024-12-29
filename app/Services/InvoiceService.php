<?php

namespace App\Services;

use App\Contracts\Repositories\InvoiceRepositoryInterface;
use App\Models\Invoice;
use App\Models\InvoiceLineItem;
use Illuminate\Support\Str;

class InvoiceService {

	private $invoiceRepository;

	public function __construct(InvoiceRepositoryInterface $invoiceRepository)
	{
		$this->invoiceRepository = $invoiceRepository;
	}

	public function getInvoiceDetails(int $id): Invoice
	{
		$invoice = $this->invoiceRepository->getInvoiceDetails($id);

		return $invoice;
	}

	public function create(array $data): Invoice
	{
		$start = $data['start_date'];
		$end = $data['end_date'];

		// Check for overlapping invoices
		$this->checkForOverlapping($data['customer_id'], $start, $end);

		$data['currency'] = 'SAR';
		$data['reference_number'] = strtoupper(Str::random(10));
		$data['total'] = 0; // Calculated later
		$invoice = $this->invoiceRepository->create($data);

		// Calculate event billing
		$this->calculateAppointmentBilling($invoice, $start, $end);
		$this->calculateActivationBilling($invoice, $start, $end);
		$this->calculateRegistrationBilling($invoice, $start, $end);

		$invoice->total = $this->calculateLineItemsTotal($invoice);
		$invoice->save();

		return $invoice;
	}

	private function calculateAppointmentBilling(Invoice $invoice, string $start, string $end)
	{
		$appointmentPrice = InvoiceLineItem::LineItemConfig['appointment']['price'];
		\DB::table('invoice_details')->insertUsing(
			['invoice_id', 'user_id', 'event', 'price', 'discount_event', 'discount_amount'],
			\DB::table('sessions')->select(
				\DB::raw("{$invoice->id} as invoice_id"),
				'user_id',
				\DB::raw("'appointment' as event"),
				\DB::raw("{$appointmentPrice} as price"),
			)
			->join('users', 'users.id', '=', 'sessions.user_id')
			->where('users.customer_id', $invoice->customer_id)
			->whereBetween('appointment', [$start, $end])
			->groupBy('user_id'),
		);
	}

	private function calculateActivationBilling(Invoice $invoice, string $start, string $end)
	{
		$activationPrice = InvoiceLineItem::LineItemConfig['activation']['price'];
		\DB::table('invoice_details')->insertUsing(
			['invoice_id', 'user_id', 'event', 'price'],
			\DB::table('sessions')->select(
				\DB::raw("{$invoice->id} as invoice_id"),
				'user_id',
				\DB::raw("'activation' as event"),
				\DB::raw("{$activationPrice} as price"),
			)
			->join('users', 'users.id', '=', 'sessions.user_id')
			->where('users.customer_id', $invoice->customer_id)
			->whereBetween('activated_at', [$start, $end])
			->whereNotIn('sessions.user_id', function($query) use ($invoice) {
				$query->select('user_id')
					->from('invoice_details')
					->where('invoice_id', $invoice->id);
			})
			->groupBy('user_id'),
		);
	}

	private function calculateRegistrationBilling(Invoice $invoice, string $start, string $end)
	{
		$registrationPrice = InvoiceLineItem::LineItemConfig['registration']['price'];
		\DB::table('invoice_details')->insertUsing(
			['invoice_id', 'user_id', 'event', 'price'],
			\DB::table('users')->select(
				\DB::raw("{$invoice->id} as invoice_id"),
				'id as user_id',
				\DB::raw("'registration' as event"),
				\DB::raw("{$registrationPrice} as price"),
			)
			->where('users.customer_id', $invoice->customer_id)
			->whereBetween('registration_date', [$start, $end])
			->whereNotIn('id', function($query) use ($invoice) {
				$query->select('user_id')
					->from('invoice_details')
					->where('invoice_id', $invoice->id);
			}),
		);
	}

	private function calculateLineItemsTotal(Invoice $invoice): float
	{
		$appointmentCount = $invoice->details()->where('event', 'appointment')->count();
		$activationCount = $invoice->details()->where('event', 'activation')->count();
		$registrationCount = $invoice->details()->where('event', 'registration')->count();

		$appointmentPrice = InvoiceLineItem::LineItemConfig['appointment']['price'];
		$activationPrice = InvoiceLineItem::LineItemConfig['activation']['price'];
		$registrationPrice = InvoiceLineItem::LineItemConfig['registration']['price'];

		InvoiceLineItem::insert([
			[
				'invoice_id' => $invoice->id,
				'name' => 'appointment',
				'quantity' => $appointmentCount,
				'price' => $appointmentPrice,
			],
			[
				'invoice_id' => $invoice->id,
				'name' => 'activation',
				'quantity' => $activationCount,
				'price' => $activationPrice,
			],
			[
				'invoice_id' => $invoice->id,
				'name' => 'registration',
				'quantity' => $registrationCount,
				'price' => $registrationPrice,
			],
		]);

		return ($appointmentCount * $appointmentPrice) + ($activationCount * $activationPrice) + ($registrationCount * $registrationPrice);
	}

	private function checkForOverlapping(int $customerId, string $start, string $end): void
	{
		$overlappingExists = $this->invoiceRepository->checkOverlappingInvoices($customerId, $start, $end);

		if ($overlappingExists) {
			abort(400, 'Cannot create overlapping invoices.');
		}
	}
}
