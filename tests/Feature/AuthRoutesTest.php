<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthenticationTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        
        $this->refreshApplication();
    }

    //Todo delete after having a seperate testing database
    public function deleteUser(){
        
        $user = User::where('name','test')->delete();

        return $user;
    }

    public function test_auth_successfull()
    {
        $response = $this->call('POST', '/api/auth/login', ['email' => 'ram.gopinath@fusionretailbrands.com.au', 'password' => 'Password6']);
        
        $result = json_decode($response->getContent());
        
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'token'
        ]);
        
        $this->assertObjectHasAttribute('token', $result, 'Token does not exists');
        
        $token = $result->token;

        return $token;
    }

    public function test_auth_invalid_detail()
    {
        $response = $this->call('POST', '/api/auth/login', ['email' => 'test@mail.com', 'password' => '12345789']);
        
        $response->assertStatus(401);

        $response->assertJsonStructure([
            'message','status_code'
        ]);
    }

    /**
     * @depends test_auth_successfull
     */
    public function test_auth_get_user($token)
    {
        $response = $this->call('GET', '/api/auth/user', ['token' => $token]);
        
        $content = json_decode($response->getContent());
        
        $response->assertStatus(200);
    }

    /**
     * @depends test_auth_successfull
     */
    public function test_auth_user_signup($token)
    {
        $response = $this->call('POST', '/api/auth/signup?token='.$token, ['name' => 'test', 'password' => '123456789', 'email' => 'test@mail.com', 'role' => 'administrator', 'role_id' => '0']);
        
        $content = json_decode($response->getContent());

        //remove after testing database creation
        $this->deleteUser();
        
        $response->assertStatus(200);
    }

    /**
    * @depends test_auth_successfull
    */
    public function test_auth_user_signup_validation_fail($token)
    {
        $response = $this->call('POST', '/api/auth/signup?token='.$token, ['name' => '', 'password' => '', 'email' => '', 'role' => '', 'role_id' => '0']);
        
        $content = json_decode($response->getContent());
        
        $response->assertStatus(422);
    }

    
}
