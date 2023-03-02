<?php

namespace EcommerceLayer\Http\Controllers;

use EcommerceLayer\Http\Resources\CustomerResource;
use EcommerceLayer\Models\Customer;
use EcommerceLayer\Services\CustomerService;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use PrinsFrank\Standards\Http\HttpStatusCode;

class CustomerController extends Controller
{
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

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

        $customer = $this->customerService->create($request->all());

        return CustomerResource::make($customer);
    }

    public function update($id, Request $request)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'metadata' => 'array',
        ]);

        $customer = $this->customerService->create($customer, $request->all());

        return CustomerResource::make($customer);
    }

    public function delete($id)
    {
        $customer = Customer::findOrFail($id);

        $this->customerService->delete($customer);

        return response()->json([], HttpStatusCode::No_Content);
    }
}
