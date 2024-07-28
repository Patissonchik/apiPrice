<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\TravelCostCalculator;

class ApiPriceController extends AbstractController
{
    private $calculator;

    public function __construct(TravelCostCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    #[Route('/api/calculate', name: 'calculate_travel_cost', methods: ['POST'])]
    public function calculate(Request $request): JsonResponse
    {
        $baseCost = $request->request->get('baseCost');
        $startDate = $request->request->get('startDate');
        $birthDate = $request->request->get('birthDate');
        $paymentDate = $request->request->get('paymentDate');
        

         //Валидация наличия всех параметров
        if (!$baseCost || !$startDate || !$birthDate || !$paymentDate) {
            return new JsonResponse(['error' => 'Invalid input data. All parameters are required.'], 400);
        }

        //Преобразование baseCost в число
        if (!is_numeric($baseCost) || $baseCost <= 0) {
            return new JsonResponse(['error' => 'Invalid base cost. It must be a positive number.'], 400);
        }

        //Преобразование строковых дат в объекты DateTime
        $startDate = \DateTime::createFromFormat('d.m.Y', $startDate);
        $birthDate = \DateTime::createFromFormat('d.m.Y', $birthDate);
        $paymentDate = \DateTime::createFromFormat('d.m.Y', $paymentDate);
        //Валидация корректности дат
        if (!$startDate || $startDate->getLastErrors() ||
            !$birthDate || $birthDate->getLastErrors() ||
            !$paymentDate || $paymentDate->getLastErrors()) {
                return new JsonResponse(['error' => 'Invalid date format. Use d.m.Y format and ensure dates are valid.'], 400);
        }
        
        //$data = json_decode($request->getContent(), true);
        //$baseCost = (float)$data['baseCost'];
        //$startDate = \DateTime::createFromFormat('d.m.Y', $data['startDate']);
        //$birthDate = \DateTime::createFromFormat('d.m.Y', $data['birthDate']);
        //$paymentDate = \DateTime::createFromFormat('d.m.Y', $data['paymentDate']);

        $baseCost = (float)$baseCost;
        $finalCost = $this->calculator->calculate($baseCost, $startDate, $birthDate, $paymentDate);

        return new JsonResponse(['Result' => $finalCost]);
    }

}
