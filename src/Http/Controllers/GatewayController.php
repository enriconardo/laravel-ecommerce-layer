<?php

namespace EcommerceLayer\Http\Controllers;

use EcommerceLayer\Http\Resources\GatewayResource;
use EcommerceLayer\Models\Gateway;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    public function list(Request $request)
    {
        $customers = QueryBuilder::for(Gateway::class)
            ->allowedFilters([AllowedFilter::exact('identifier')])
            ->paginate()
            ->appends($request->query());

        return GatewayResource::collection($customers);
    }

    public function find($id)
    {
        /** @var Gateway $gateway */
        $gateway = Gateway::findOrFail($id);

        return GatewayResource::make($gateway);
    }
}
