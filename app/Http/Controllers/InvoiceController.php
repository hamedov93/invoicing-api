<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\InvoiceResource;
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

    public function details(int $id)
    {
        $invoice = $this->invoiceService->getInvoiceDetails($id);

        return new InvoiceResource($invoice);
    }
}
