<?php

namespace App\Http\Resources\Sales;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Sellers\SellerResource;

class SaleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'seller_id'  => $this->seller_id,
            'amount'     => (float) $this->amount,
            'commission' => (float) $this->commission,
            'sold_at'    => $this->sold_at?->toDateString(),
            'created_at' => $this->created_at?->toISOString(),
            'seller'     => SellerResource::make($this->whenLoaded('seller')),
        ];
    }
}
