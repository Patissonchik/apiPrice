<?php
namespace App\Tests\Service;

use App\Service\TravelCostCalculator;
use PHPUnit\Framework\TestCase;

class TravelCostCalculatorTest extends TestCase
{
    private $calculator;

    protected function setUp(): void
    {
        $this->calculator = new TravelCostCalculator();
    }

    public function testWithChildDiscount()
    {
        //Ребенок младше 3 лет (без скидки)
        $this->assertEquals(970, $this->calculator->calculate(1000, new \DateTime('01.06.2023'), new \DateTime('01.01.2021'), new \DateTime('01.01.2023')));
        //Ребенок от 3 до 6 лет (скидка 80%)
        $this->assertEquals(194, $this->calculator->calculate(1000, new \DateTime('01.06.2023'), new \DateTime('01.01.2018'), new \DateTime('01.01.2023')));
        //Ребенок от 6 до 12 лет (скидка 30% или 4500)
        $this->assertEquals(679, $this->calculator->calculate(1000, new \DateTime('01.06.2023'), new \DateTime('01.01.2012'), new \DateTime('01.01.2023')));
        $this->assertEquals(44135, $this->calculator->calculate(50000, new \DateTime('01.06.2023'), new \DateTime('01.01.2012'), new \DateTime('01.01.2023')));
        //Ребенок от 12 до 18 лет (скидка 10%)
        $this->assertEquals(873, $this->calculator->calculate(1000, new \DateTime('01.06.2023'), new \DateTime('01.01.2008'), new \DateTime('01.01.2023')));
    }

    public function testEarlyBookingDiscount()
    {
        //с 1 апреля по 30 сентября
        $this->assertEquals(930, $this->calculator->calculate(1000, new \DateTime('01.05.2023'), new \DateTime('01.01.2000'), new \DateTime('01.11.2022')));
        $this->assertEquals(950, $this->calculator->calculate(1000, new \DateTime('01.05.2023'), new \DateTime('01.01.2000'), new \DateTime('31.12.2022')));
        $this->assertEquals(970, $this->calculator->calculate(1000, new \DateTime('01.05.2023'), new \DateTime('01.01.2000'), new \DateTime('31.01.2023')));
        //с 1 октября текущего года
        $this->assertEquals(930, $this->calculator->calculate(1000, new \DateTime('01.11.2023'), new \DateTime('01.01.2000'), new \DateTime('01.03.2023')));
        $this->assertEquals(950, $this->calculator->calculate(1000, new \DateTime('01.11.2023'), new \DateTime('01.01.2000'), new \DateTime('15.04.2023')));
        $this->assertEquals(970, $this->calculator->calculate(1000, new \DateTime('01.11.2023'), new \DateTime('01.01.2000'), new \DateTime('31.05.2023')));
        //до 14 января следующего
        $this->assertEquals(930, $this->calculator->calculate(1000, new \DateTime('02.01.2023'), new \DateTime('01.01.2000'), new \DateTime('01.03.2022')));
        $this->assertEquals(950, $this->calculator->calculate(1000, new \DateTime('02.01.2023'), new \DateTime('01.01.2000'), new \DateTime('30.04.2022')));
        $this->assertEquals(970, $this->calculator->calculate(1000, new \DateTime('02.01.2023'), new \DateTime('01.01.2000'), new \DateTime('31.05.2022')));
        //с 15 января следующего года и далее
        $this->assertEquals(930, $this->calculator->calculate(1000, new \DateTime('02.02.2023'), new \DateTime('01.01.2000'), new \DateTime('01.08.2022')));
        $this->assertEquals(950, $this->calculator->calculate(1000, new \DateTime('02.02.2023'), new \DateTime('01.01.2000'), new \DateTime('30.09.2022')));
        $this->assertEquals(970, $this->calculator->calculate(1000, new \DateTime('02.02.2023'), new \DateTime('01.01.2000'), new \DateTime('31.10.2022')));
        
    }

    public function testNoDiscounts()
    {
        $this->assertEquals(1000, $this->calculator->calculate(1000, new \DateTime('01.06.2023'), new \DateTime('01.01.2000'), new \DateTime('01.02.2023')));
    }
}
