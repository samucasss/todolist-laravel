<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Models\User;

class AuthControllerTest extends TestCase {

    protected function setUp(): void {
        parent::setUp();
        User::query()->delete();
    }

    public function test_register_ok() {
        $user = new User;
        $user->name = 'Samuel';
        $user->email = 'samuca@gmail.com';
        $user->password = 'samuca';

        printf('user: '. json_encode($user->toArray()));
        $response = $this->postJson('api/auth/register', $user->toArray());

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereNot('id', null)
                 ->where('password', null)
                 ->where('name', 'Samuel')
                 ->where('email', 'samuca@gmail.com')
                 ->etc()
        );            
    }

    public function test_register_sem_nome() {
        $user = new User;
        $user->email = 'samuca@gmail.com';
        $user->password = 'samuca';

        $response = $this->postJson('api/auth/register', $user->toArray());
        $response->assertStatus(422);
    }

    public function test_register_sem_email() {
        $user = new User;
        $user->name = 'Samuel';
        $user->password = 'samuca';

        $response = $this->postJson('api/auth/register', $user->toArray());
        $response->assertStatus(422);
    }

    public function test_register_sem_senha() {
        $user = new User;
        $user->name = 'Samuel';
        $user->email = 'samuca@gmail.com';

        $response = $this->postJson('api/auth/register', $user->toArray());
        $response->assertStatus(422);
    }

    public function test_login_ok() {
        $this->saveUsuario(); 

        $credentials = ['email' => 'samuca@gmail.com', 'password' => 'samuca'];
        $response = $this->postJson('api/auth/login', $credentials);

        $response->assertStatus(200);
        $this->assertNotNull($response->json()['token']);
    }

    public function test_login_senha_incorreta() {
        $this->saveUsuario(); 

        $credentials = ['email' => 'samuca@gmail.com', 'password' => 'samuca11'];
        $response = $this->postJson('api/auth/login', $credentials);

        $response->assertStatus(401);
    }

    public function test_login_email_inexistente() {
        $this->saveUsuario(); 

        $credentials = ['email' => 'samucasss@gmail.com', 'password' => 'samuca'];
        $response = $this->postJson('api/auth/login', $credentials);

        $response->assertStatus(401);
    }

    public function test_login_campos_nao_preenchidos() {
        $this->saveUsuario(); 

        $credentials = ['email' => '', 'password' => ''];
        $response = $this->postJson('api/auth/login', $credentials);

        $response->assertStatus(401);
    }

    public function test_get_ok() {
        $this->saveUsuario(); 
        $token = $this->login('samuca@gmail.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->get('api/auth/get');

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereNot('id', null)
                 ->where('password', null)
                 ->where('name', 'Samuel')
                 ->where('email', 'samuca@gmail.com')
                 ->etc()
        );            
    }

    public function test_get_sem_autenticacao() {
        $this->saveUsuario(); 

        $response = $this->get('api/auth/get');
        $response->assertStatus(401);
    }

    private function saveUsuario() {
        $user = new User;
        $user->name = 'Samuel';
        $user->email = 'samuca@gmail.com';

        $password = bcrypt('samuca');
        $user->password = $password;

        $user->save();
    }

    private function login(string $email): string {
        $credentials = ['email' => $email, 'password' => 'samuca'];
        $token = auth()->attempt($credentials);
        return $token;
    }

}
