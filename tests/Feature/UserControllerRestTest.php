<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Models\User;

class UserRestControllerTest extends TestCase {

    protected function setUp(): void {
        parent::setUp();
        User::query()->delete();
        $this->saveUsuario(); 
    }

    public function test_save_alteracao_ok() {
        $token = $this->login('samuca@gmail.com');

        $user = new User;
        $user->name = 'Samuel Santos';
        $user->email = 'samucasss@gmail.com';
        $user->password = 'samucasss';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('api/usuarios', $user->toArray());

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereNot('id', null)
                 ->where('password', null)
                 ->where('name', 'Samuel Santos')
                 ->where('email', 'samucasss@gmail.com')
                 ->etc()
        );            
    }

    public function test_save_alteracao_sem_email() {
        $token = $this->login('samuca@gmail.com');

        $user = new User;
        $user->name = 'Samuel Santos';
        $user->password = 'samucasss';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('api/usuarios', $user->toArray());

        $response->assertStatus(422);
    }

    public function test_save_alteracao_sem_nome() {
        $token = $this->login('samuca@gmail.com');

        $user = new User;
        $user->email = 'samucasss@gmail.com';
        $user->password = 'samucasss';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('api/usuarios', $user->toArray());

        $response->assertStatus(422);
    }

    public function test_save_alteracao_sem_senha() {
        $token = $this->login('samuca@gmail.com');

        $user = new User;
        $user->name = 'Samuel Santos';
        $user->email = 'samucasss@gmail.com';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('api/usuarios', $user->toArray());

        $response->assertStatus(422);
    }

    public function test_save_alteracao_sem_autenticacao() {
        $user = new User;
        $user->name = 'Samuel Santos';
        $user->email = 'samucasss@gmail.com';
        $user->password = 'samucasss';

        $response = $this->postJson('api/usuarios', $user->toArray());

        $response->assertStatus(401);
    }

    public function test_delete_ok() {
        $token = $this->login('samuca@gmail.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->delete('api/usuario');

        $response->assertStatus(200);
        $this->assertEquals('OK', $response->content()); 
    }

    public function test_delete_sem_autenticacao() {
        $response = $this->delete('api/usuario');

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
