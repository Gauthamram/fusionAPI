<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TicketTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        
        $this->setToken();

        $this->refreshApplication();
    }

    public function test_tickets_listing()
    {
        $response = $this->call('GET', '/api/tickets/', ['token' => $this->token]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data'
        ]);
    }

    public function test_tips_tickets_printed_listing()
    {
        $response = $this->call('GET', '/api/ticket/tips/printed', ['token' => $this->token]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data'
        ]);
    }
    /**
     * TODO write tests for other routes
     */
}
