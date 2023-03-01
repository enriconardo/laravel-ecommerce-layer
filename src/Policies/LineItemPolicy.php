<?php

namespace EnricoNardo\EcommerceLayer\Policies;

use EnricoNardo\EcommerceLayer\Models\Order;
use Illuminate\Auth\Access\Response;
use EnricoNardo\EcommerceLayer\Enums\OrderStatus;
use EnricoNardo\EcommerceLayer\Models\LineItem;

class LineItemPolicy
{
    public function create(Order $order): Response
    {
        if ($order->status !== OrderStatus::DRAFT) {
            return Response::deny("You can add line items only to carts (draft orders).");
        }
    }

    public function update(LineItem $lineItem): Response
    {
        if ($lineItem->order->status !== OrderStatus::DRAFT) {
            return Response::deny("Only line items belonging to a cart (draft order) can be updated");
        }
    }

    public function delete(LineItem $lineItem): Response
    {
        return $lineItem->order->status === OrderStatus::DRAFT
            ? Response::allow()
            : Response::deny("Only line items belonging to a cart (draft order) can be deleted");
    }
}
