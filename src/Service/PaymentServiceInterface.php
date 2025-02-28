<?php

namespace App\Service;

interface PaymentServiceInterface
{
    public function purchase(array $data): array;
}