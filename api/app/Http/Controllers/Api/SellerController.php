<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sellers\StoreRequest;
use App\Http\Requests\Sellers\UpdateRequest;
use App\Http\Resources\Sellers\SellerResource;
use App\Models\Seller;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function index(Request $request)
    {
        $perPage    = (int) $request->integer('per_page', 50);
        $q          = Seller::query()->orderBy('name');

        if ($perPage > 0) {
            return SellerResource::collection($q->paginate($perPage));
        }

        return SellerResource::collection($q->get());
    }

    public function store(StoreRequest $request)
    {
        //dd($request->validated());
        $seller = Seller::create($request->validated());

        return (new SellerResource($seller))
            ->response()
            ->setStatusCode(201)
            ->header('Location', route('sellers.show', $seller));
    }

    public function show(Seller $seller)
    {
        return new SellerResource($seller);
    }

    public function update(UpdateRequest $request, Seller $seller)
    {
        $seller->update($request->validated());
        return new SellerResource($seller);
    }

    public function destroy(Seller $seller)
    {
        $seller->delete();
        return response()->noContent();
    }
}
