<?php

namespace EcommerceLayer\Http\Controllers;

use EcommerceLayer\Enums\SubscriptionStatus;
use EcommerceLayer\Events\Subscriptions\SubscriptionCanceled;
use EcommerceLayer\Http\Resources\SubscriptionResource;
use EcommerceLayer\Models\Subscription;
use EcommerceLayer\Services\SubscriptionService;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SubscriptionController extends Controller
{
    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function list(Request $request)
    {
        $subs = QueryBuilder::for(Subscription::class)
            ->allowedFilters([
                AllowedFilter::exact('status'), 
                AllowedFilter::exact('customer.id'),
                AllowedFilter::exact('price.id'),
                'expired_at',
                'started_at'
            ])
            ->allowedSorts(['started_at', 'expired_at'])
            ->paginate()
            ->appends($request->query());

        return SubscriptionResource::collection($subs);
    }

    public function find($id)
    {
        $sub = Subscription::findOrFail($id);
        
        return SubscriptionResource::make($sub);
    }

    public function cancel($id)
    {
        $sub = Subscription::findOrFail($id);

        $sub = $this->subscriptionService->update($sub, [
            'status' => SubscriptionStatus::CANCELED
        ]);

        SubscriptionCanceled::dispatch($sub);

        return SubscriptionResource::make($sub);
    }
}
