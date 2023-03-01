<?php

namespace EnricoNardo\EcommerceLayer\Policies;

use EnricoNardo\EcommerceLayer\Enums\OrderStatus;
use EnricoNardo\EcommerceLayer\Models\Order;
use Illuminate\Auth\Access\Response;
use PrinsFrank\Standards\Http\HttpStatusCode;

class OrderPolicy
{
    public function update(Order $order): Response
    {
        // Only cart (draft order) can be updated
        if ($order->status !== OrderStatus::DRAFT) {
            return Response::deny("Only cart (draft order) can be updated");
        }
    }

    public function place(Order $order): Response
    {
        // Only cart (draft order) can be placed
        if ($order->status !== OrderStatus::DRAFT) {
            return Response::deny("Only cart (draft order) can be placed");
        }

        // Only non empty order can be placed
        if ($order->line_items->count() === 0) {
            return Response::denyWithStatus(
                HttpStatusCode::Bad_Request, 
                "An empty order cannot be placed. Add at least one line item to it."
            );
        }
    }

    public function delete(Order $order): Response
    {
        // Only cart (draft orders) can be deleted
        return $order->status === OrderStatus::DRAFT
            ? Response::allow()
            : Response::deny("Only cart (draft orders) can be deleted");
    }
}
