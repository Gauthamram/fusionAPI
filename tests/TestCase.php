<?php
namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    public function setToken()
    {
        $response = $this->Json('POST', '/api/auth/login', ['email' => 'ram.gopinath@fusionretailbrands.com.au', 'password' => 'Password6']);
        
        $content = json_decode($response->getContent());
        
        $this->assertObjectHasAttribute('token', $content, 'Token does not exists');
        
        $this->token = $content->token;
        
        return $this;
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}
