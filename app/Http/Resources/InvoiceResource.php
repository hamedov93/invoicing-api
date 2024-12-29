<?php

namespace App\Http\Resources;

use App\Models\InvoiceDetail;
use App\Models\InvoiceLineItem;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'reference_number' => $this->reference_number,
            'customer' => [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
            ],
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'total' => $this->total,
            'currency' => $this->currency,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'events' => $this->lineItems->transform(function (InvoiceLineItem $item) {
                return [
                    'name' => $item->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ];
            }),
            'users' => $this->details->transform(function (InvoiceDetail $detail) {
                return [
                    'id' => $detail->user->id,
                    'name' => $detail->user->name,
                    'email' => $detail->user->email,
                    'billing_event' => $detail->event,
                    'billing_price' => $detail->price,
                ];
            }),
        ];
    }
}
