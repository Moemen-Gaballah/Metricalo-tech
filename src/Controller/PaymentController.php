<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\PaymentServiceInterface;

class PaymentController extends AbstractController
{
    public function purchase(string $provider, Request $request): JsonResponse
    {
        $service = $this->resolveService($provider);

        if (!$service) {
            return new JsonResponse(['error' => 'Invalid provider'], 400);
        }

        try {
            $response = $service->purchase($request->toArray());
            return new JsonResponse($response);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    private function resolveService(string $provider): ?PaymentServiceInterface
    {
        return match($provider) {
            'shift4' => $this->container->get('App\Service\Shift4Service'),
            'aci' => $this->container->get('App\Service\AciService'),
            default => null,
        };
    }
}
