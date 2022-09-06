<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Models\Preferencias;
use App\Models\User;

class PreferenciasRestControllerTest extends TestCase {

    protected function setUp(): void {
        parent::setUp();
        Preferencias::query()->delete();
        User::query()->delete();
    }

    public function test_save_ok() {
        $this->saveUsuario();
        $token = $this->login('samuca@gmail.com');

        $preferencias = new Preferencias;
        $preferencias->tipoFiltro = 'T';
        $preferencias->done = false;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('api/preferencias', $preferencias->toArray());

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereNot('id', null)
                 ->where('tipoFiltro', 'T')
                 ->where('done', false)
                 ->etc()
        );            
    }

    public function test_save_sem_tipo_filtro() {
        $this->saveUsuario();
        $token = $this->login('samuca@gmail.com');

        $preferencias = new Preferencias;
        $preferencias->done = false;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('api/preferencias', $preferencias->toArray());

        $response->assertStatus(422);
    }

    public function test_save_sem_done() {
        $this->saveUsuario();
        $token = $this->login('samuca@gmail.com');

        $preferencias = new Preferencias;
        $preferencias->tipoFiltro = 'T';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('api/preferencias', $preferencias->toArray());

        $response->assertStatus(422);
    }

    public function test_save_sem_autenticacao() {
        $this->saveUsuario();

        $preferencias = new Preferencias;
        $preferencias->tipoFiltro = 'T';
        $preferencias->done = false;

        $response = $this->postJson('api/preferencias', $preferencias->toArray());        

        $response->assertStatus(401);
    }

    public function test_save_alteracao_ok() {
        $user = $this->saveUsuario();
        $this->savePreferencias($user);
        $token = $this->login('samuca@gmail.com');

        $preferencias = new Preferencias;
        $preferencias->tipoFiltro = 'H';
        $preferencias->done = true;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('api/preferencias', $preferencias->toArray());

        $preferenciasAll = $preferencias::get();
        $count = count($preferenciasAll);

        $this->assertEquals(1, $count);

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereNot('id', null)
                ->where('tipoFiltro', 'H')
                 ->where('done', true)
                 ->etc()
        );            
    }

    public function test_save_alteracao_sem_tipo_filtro() {
        $user = $this->saveUsuario();
        $this->savePreferencias($user);
        $token = $this->login('samuca@gmail.com');

        $preferencias = new Preferencias;
        $preferencias->done = true;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('api/preferencias', $preferencias->toArray());

        $response->assertStatus(422);
    }

    public function test_save_alteracao_sem_done() {
        $user = $this->saveUsuario();
        $this->savePreferencias($user);
        $token = $this->login('samuca@gmail.com');

        $preferencias = new Preferencias;
        $preferencias->tipoFiltro = 'H';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('api/preferencias', $preferencias->toArray());

        $response->assertStatus(422);
    }

    public function test_save_alteracao_sem_autenticacao() {
        $user = $this->saveUsuario();
        $this->savePreferencias($user);

        $preferencias = new Preferencias;
        $preferencias->tipoFiltro = 'H';
        $preferencias->done = true;

        $response = $this->postJson('api/preferencias', $preferencias->toArray());
        $response->assertStatus(401);
    }

    public function test_get_ok() {
        $user = $this->saveUsuario();
        $this->savePreferencias($user);
        $token = $this->login('samuca@gmail.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->get('api/preferencia');

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereNot('id', null)
                 ->where('tipoFiltro', 'T')
                 ->where('done', false)
                 ->etc()
        );            
    }

    public function test_get_sem_autenticacao() {
        $user = $this->saveUsuario();
        $this->savePreferencias($user);

        $response = $this->get('api/preferencia');
        $response->assertStatus(401);
    }

    public function test_delete_ok() {
        $user = $this->saveUsuario();
        $this->savePreferencias($user);
        $token = $this->login('samuca@gmail.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->delete('api/preferencia');

        $response->assertStatus(200);
        $this->assertEquals('OK', $response->content()); 
    }

    public function test_delete_sem_autenticacao() {
        $user = $this->saveUsuario();
        $this->savePreferencias($user);

        $response = $this->delete('api/preferencia');

        $response->assertStatus(401);
    }

    private function savePreferencias(User $user) {
        $preferencias = new Preferencias;
        $preferencias->tipoFiltro = 'T';
        $preferencias->done = false;
        $preferencias->usuarioId = $user->id;

        $preferencias->save();
    }

    private function saveUsuario(): User {
        $user = new User;
        $user->name = 'Samuel';
        $user->email = 'samuca@gmail.com';

        $password = bcrypt('samuca');
        $user->password = $password;

        $user->save();

        return $user;
    }

    private function login(string $email): string {
        $credentials = ['email' => $email, 'password' => 'samuca'];
        $token = auth()->attempt($credentials);
        return $token;
    }

}
