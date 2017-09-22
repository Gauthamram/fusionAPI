<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        
        $this->setToken();

        $this->refreshApplication();
    }

    public function test_suppliers_listing()
    {
        $response = $this->call('GET', '/api/suppliers/', ['token' => $this->token]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data'
        ]);

        $result = json_decode($response->getContent(), true);

        $params =  ['supplier' => $result['data'][0]['id'], 'name' => $result['data'][0]['name']];

        return $params;
    }

    /**
     * @depends test_suppliers_listing
     */
    public function test_supplier_data($params)
    {
        $response = $this->call('GET', '/api/supplier/'.$params['supplier'], ['token' => $this->token]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data'
        ]);
    }

    /**
     * @depends test_suppliers_listing
     */
    public function test_supplier_search_data($params)
    {
        $response = $this->call('GET', '/api/supplier/search/'.substr($params['name'], 0, 4), ['token' => $this->token]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data'
        ]);
    }
    /**
     * TODO write tests for other routes
     */
}
