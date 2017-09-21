<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrdersTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setToken();
        
        $this->refreshApplication();
    }
    /**
     * A basic functional test for all the order routes.
     *
     * @return void
     */
    public function testit_returns_orders_list_data()
    {
        $response = $this->call('GET', '/api/orders/', ['token' => $this->token]);

        $response->assertStatus(200);
        
        $result = json_decode($response->getContent(), true);
        
        $order_number = $result['data'][0]['order_number'];
        
        $response->assertJsonStructure([
            'data' => [['order_number','supplier','approval_date']]
        ]);

        return $order_number;
    }

    /**
     * @depends testit_returns_orders_list_data
     */
    public function testit_returns_an_order_data($order_number)
    {
        $response = $this->call('GET', '/api/order/'.$order_number, ['token' => $this->token]);

        $response->assertStatus(200);
        
        $response->assertJsonStructure([
            'data' => [['order_number','supplier','approval_date']]
        ]);
    }

    /**
     * @depends testit_returns_orders_list_data
     */
    public function testit_returns_an_order_details_data($order_number)
    {
        $response = $this->call('GET', '/api/order/details/'.$order_number, ['token' => $this->token]);
        
        $result = json_decode($response->getContent(), true);
        
        $params = ['order_number' => $result['data'][0]['order_no'], 'item_number' => $result['data'][0]['item']];
        
        $response->assertStatus(200);
        
        $response->assertJsonStructure([
            'data' => [['order_no','style','item','location','location_type','qty','country','retail','pack','simple_pack_ind']]
        ]);

        return $params;
    }

    /**
     * @depends testit_returns_an_order_details_data
     */
    public function testit_returns_an_order_cartonpack_optional_itemnumber_data($params)
    {
        $response = $this->call('GET', '/api/order/'.$params['order_number'].'/cartonpack', ['token' => $this->token]);

        $response->assertStatus(200);
        
        $result = json_decode($response->getContent(), true);

        if (!empty($result['data'])) {
            $response->assertJson([
                'data' => true,
            ]);
        }
    }

    /**
     * @depends testit_returns_an_order_details_data
     */
    public function testit_returns_an_order_cartonpack_with_itemnumber_data($params)
    {
        $response = $this->call('GET', '/api/order/'.$params['order_number'].'/cartonpack/'.$params['item_number'], ['token' => $this->token]);

        $response->assertStatus(200);
        
        $result = json_decode($response->getContent(), true);

        if (!empty($result['data'])) {
            $response->assertJson([
                'data' => true,
            ]);
        }
    }

    /**
     * @depends testit_returns_an_order_details_data
     */
    public function testit_returns_an_order_cartonloose_optional_itemnumber_data($params)
    {
        $response = $this->call('GET', '/api/order/'.$params['order_number'].'/cartonloose', ['token' => $this->token]);

        $response->assertStatus(200);
        
        $result = json_decode($response->getContent(), true);

        if (!empty($result['data'])) {
            $response->assertJson([
                'data' => true,
            ]);
        }
    }

    /**
     * @depends testit_returns_an_order_details_data
     */
    public function testit_returns_an_order_cartonloose_with_itemnumber_data($params)
    {
        $response = $this->call('GET', '/api/order/'.$params['order_number'].'/cartonloose/'.$params['item_number'], ['token' => $this->token]);

        $response->assertStatus(200);
        
        $result = json_decode($response->getContent(), true);

        if (!empty($result['data'])) {
            $response->assertJson([
                'data' => true,
            ]);
        }
    }

    /**
     * @depends testit_returns_orders_list_data
     */
    public function testit_returns_an_order_ratiopack_data($order_number)
    {
        $response = $this->call('GET', '/api/order/'.$order_number.'/ratiopack', ['token' => $this->token]);
        
        $response->assertStatus(200);
        
        // $response->assertJson([
        //         'data' => true,
        //     ]);

        // return $params;
    }

    /**
     * @depends testit_returns_orders_list_data
     */
    public function testit_returns_an_order_simplepack_data($order_number)
    {
        $response = $this->call('GET', '/api/order/'.$order_number.'/simplepack', ['token' => $this->token]);
        
        $response->assertStatus(200);
        
        // $response->assertJson([
        //         'data' => true,
        //     ]);
    }

    /**
     * @depends testit_returns_orders_list_data
     */
    public function testit_returns_an_order_looseitem_data($order_number)
    {
        $response = $this->call('GET', '/api/order/'.$order_number.'/looseitem', ['token' => $this->token]);
        
        $response->assertStatus(200);
        
        // $response->assertJson([
        //         'data' => true,
        //     ]);

        // return $params;
    }
}
