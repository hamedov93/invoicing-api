<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Services\InvoiceService;

class InvoiceController extends Controller
{
    private $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function create(InvoiceRequest $request)
    {
        $data = $request->validated();

        $invoice = $this->invoiceService->create($data);

        return response()->json([
            'invoice_id' => $invoice->id,
        ]);
    }
}
