<?php

namespace EnricoNardo\EcommerceLayer\Gateways;

interface CustomerServiceInterface
{
    public function create($email, $billingAddress = null, $metadata = null);

    public function update($email, $billingAddress = null, $metadata = null);

    public function findByEmail($email);
}