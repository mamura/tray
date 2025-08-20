<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\IndexRequest;
use App\Http\Requests\Sales\StoreRequest;
use App\Http\Requests\Sales\UpdateRequest;
use App\Http\Resources\Sales\SaleResource;
use App\Models\Sale;
use App\Models\Seller;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(IndexRequest $request)
    {
        $perPage = (int) $request->integer('per_page', 15);

        $q = Sale::query()
            ->with('seller')
            ->when($request->seller_id, fn ($q) => $q->ofSeller((int) $request->seller_id))
            ->soldBetween($request->date_from, $request->date_to)
            ->orderByDesc('sold_at')
            ->orderByDesc('id');

        return SaleResource::collection($q->paginate($perPage));
    }

    public function bySeller(IndexRequest $request, Seller $seller)
    {
        $perPage = (int) $request->integer('per_page', 15);

        $q = $seller->sales()
            ->with('seller')
            ->soldBetween($request->date_from, $request->date_to)
            ->orderByDesc('sold_at')
            ->orderByDesc('id');

        return SaleResource::collection($q->paginate($perPage));
    }

    public function store(StoreRequest $request)
    {
        $sale = Sale::create($request->validated());
        $sale->load('seller');

        return (new SaleResource($sale))
            ->response()
            ->setStatusCode(201)
            ->header('Location', route('sales.show', $sale));
    }

    public function show(Sale $sale)
    {
        $sale->load('seller');
        return new SaleResource($sale);
    }

    public function update(UpdateRequest $request, Sale $sale)
    {
        $sale->update($request->validated());
        $sale->load('seller');

        return new SaleResource($sale);
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();
        return response()->noContent();
    }
}
