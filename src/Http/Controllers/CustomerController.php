<?php

namespace EnricoNardo\EcommerceLayer\Http\Controllers;

use EnricoNardo\EcommerceLayer\Events\CustomerDeleted;
use EnricoNardo\EcommerceLayer\Http\Resources\CustomerResource;
use EnricoNardo\EcommerceLayer\ModelBuilders\CustomerBuilder;
use EnricoNardo\EcommerceLayer\Models\Customer;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use PrinsFrank\Standards\Http\HttpStatusCode;

class CustomerController extends Controller
{
    public function list(Request $request)
    {
        $customers = QueryBuilder::for(Customer::class)
            ->allowedFilters([AllowedFilter::exact('email')])
            ->paginate()
            ->appends($request->query());

        return CustomerResource::collection($customers);
    }

    public function find($id)
    {
        /** @var Customer $customer */
        $customer = Customer::findOrFail($id);

        return Customer::make($customer);
    }

    public function create(Request $request)
    {
        $request->validate([
            'email' => 'email|required',
            'metadata' => 'array',
        ]);

        $data = [
            'email' => $request->input('email'),
            'metadata' => $request->input('metadata'),
        ];

        $customer = CustomerBuilder::init()->fill($data)->end();

        return CustomerResource::make($customer);
    }

    public function update($id, Request $request)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'metadata' => 'array',
        ]);

        $data = [
            'metadata' => $request->input('metadata'),
        ];

        $customer = CustomerBuilder::init($customer)->fill($data)->end();

        return CustomerResource::make($customer);
    }

    public function delete($id)
    {
        $customer = Customer::findOrFail($id);

        $customer->delete();

        CustomerDeleted::dispatch($customer);

        return response()->json([], HttpStatusCode::No_Content);
    }
}
