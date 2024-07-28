<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiPriceControllerTest extends WebTestCase
{
    public function testCalculateEndpoint()
    {
        $client = static::createClient();

        $client->request('POST', '/api/calculate', [
            'baseCost' => 1000,
            'startDate' => '01.06.2023',
            'birthDate' => '01.01.2010',
            'paymentDate' => '01.01.2023'
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('Result', $response);
        $this->assertEquals(873, $response['Result']);
    }

    public function testCalculateEndpointMissingParameters()
    {
        $client = static::createClient();

        //Отсутствует baseCost
        $client->request('POST', '/api/calculate', [
            'startDate' => '01.06.2023',
            'birthDate' => '01.01.2010',
            'paymentDate' => '01.01.2023'
        ]);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Invalid input data. All parameters are required.', $response['error']);
    }

    public function testCalculateEndpointInvalidBaseCost()
    {
        $client = static::createClient();

        //Некорректный baseCost
        $client->request('POST', '/api/calculate', [
            'baseCost' => 'invalid',
            'startDate' => '01.06.2023',
            'birthDate' => '01.01.2010',
            'paymentDate' => '01.01.2023'
        ]);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Invalid base cost. It must be a positive number.', $response['error']);
    }

    public function testCalculateEndpointInvalidDates()
    {
        $client = static::createClient();

        //Некорректные даты
        $client->request('POST', '/api/calculate', [
            'baseCost' => 1000,
            'startDate' => '30.02.2023', 
            'birthDate' => '31.11.2010', 
            'paymentDate' => '31.04.2023'
        ]);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Invalid date format. Use d.m.Y format and ensure dates are valid.', $response['error']);
    }
}
