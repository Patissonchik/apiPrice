<?php
namespace App\Service;

class TravelCostCalculator
{
    public function calculate(float $baseCost, \DateTime $startDate, \DateTime $birthDate, \DateTime $paymentDate): float
    {
        $age = $startDate->diff($birthDate)->y;
        $costAfterChildDiscount = $this->applyChildDiscount($baseCost, $age);
        $finalCost = $this->applyEarlyBookingDiscount($costAfterChildDiscount, $startDate, $paymentDate);
        return $finalCost;
    }

    private function applyChildDiscount(float $baseCost, int $age): float
    {   
        if ($age < 3) {
            return $baseCost;
        } elseif ($age < 6) {
            return $baseCost * 0.2;
        } elseif ($age < 12) {
            return max($baseCost * 0.7, $baseCost - 4500);
        } elseif ($age < 18) {
            return $baseCost * 0.9;
        }
        return $baseCost;
    }

    private function applyEarlyBookingDiscount(float $cost, \DateTime $startDate, \DateTime $paymentDate): float
    {
        $nextYear = (int)$startDate->format('Y');
        $year = $nextYear - 1;
        $startApril = new \DateTime("01.04.$nextYear");
        $startSeptember = new \DateTime("30.09.$nextYear");

        if ($startDate >= $startApril && $startDate <= $startSeptember) {
            if ($paymentDate <= new \DateTime("30.11.$year")) {
                return max($cost * 0.93, $cost - 1500);
            } elseif ($paymentDate <= new \DateTime("31.12.$year")) {
                return max($cost * 0.95, $cost - 1500);
            } elseif ($paymentDate <= new \DateTime("31.01.$nextYear")) {
                return max($cost * 0.97, $cost - 1500);
            }

        }

        // Путешествия с датой старта с 1 октября текущего года по 14 января следующего года
        $currentYearOctober = new \DateTime("01.10.$nextYear");
        $nextYearJanuary14 = new \DateTime("14.01.$nextYear");

        if ($startDate >= $currentYearOctober ) {
            if ($paymentDate <= new \DateTime("31.03.$nextYear")) {
                return max($cost * 0.93, $cost - 1500);
            } elseif ($paymentDate <= new \DateTime("30.04.$nextYear")) {
                return max($cost * 0.95, $cost - 1500);
            } elseif ($paymentDate <= new \DateTime("31.05.$nextYear")) {
                return max($cost * 0.97, $cost - 1500);
            }
        } elseif ($startDate <= $nextYearJanuary14){
            if ($paymentDate <= new \DateTime("31.03.$year")) {
                return max($cost * 0.93, $cost - 1500);
            } elseif ($paymentDate <= new \DateTime("30.04.$year")) {
                return max($cost * 0.95, $cost - 1500);
            } elseif ($paymentDate <= new \DateTime("31.05.$year")) {
                return max($cost * 0.97, $cost - 1500);
            }
        }

        // Путешествия с датой старта с 15 января следующего года и далее
        $nextYearJanuary15 = new \DateTime("15.01.$nextYear");

        if ($startDate >= $nextYearJanuary15) {
            if ($paymentDate <= new \DateTime("31.08.$year")) {
                return max($cost * 0.93, $cost - 1500);
            } elseif ($paymentDate <= new \DateTime("30.09.$year")) {
                return max($cost * 0.95, $cost - 1500);
            } elseif ($paymentDate <= new \DateTime("31.10.$year")) {
                return max($cost * 0.97, $cost - 1500);
            }
        }

        return $cost;
    }

}