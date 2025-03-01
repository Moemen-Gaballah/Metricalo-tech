<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\PaymentServiceInterface;
use App\Service\Shift4Service;
use App\Service\AciService;
use Symfony\Component\Routing\Attribute\Route;

class PaymentController extends AbstractController
{
    private Shift4Service $shift4Service;
    private AciService $aciService;

    public function __construct(Shift4Service $shift4Service, AciService $aciService)
    {
        $this->shift4Service = $shift4Service;
        $this->aciService = $aciService;
    }

    #[Route('/app/example/{provider}', name: 'payment', methods: ['POST'])]
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
        return match ($provider) {
            'shift4' => $this->shift4Service,
            'aci' => $this->aciService,
            default => null,
        };
    }
}
