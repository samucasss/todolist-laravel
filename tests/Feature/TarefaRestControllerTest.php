<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Models\Tarefa;
use App\Models\User;

class TarefaRestControllerTest extends TestCase {

    protected function setUp(): void {
        parent::setUp();
        Tarefa::query()->delete();
        User::query()->delete();
    }

    public function test_find_all() {
        $user = $this->saveUsuario();
        $this->saveTarefas($user);
        $token = $this->login('samuca@gmail.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->get('api/tarefas?inicio=2022-08-20&fim=2022-08-31');

        $response->assertStatus(200);

        $tarefas = $response->json();
        $this->assertEquals(2, count($tarefas));

        $tarefa1 = $tarefas[0];
        $tarefa2 = $tarefas[1];

        $this->assertNotNull($tarefa1['id']);
        $this->assertEquals(strtotime("2022/08/26"), $tarefa1['data']);
        $this->assertEquals('Tarefa 1', $tarefa1['nome']);
        $this->assertEquals('Descrição Tarefa 1', $tarefa1['descricao']);
        $this->assertEquals(false, $tarefa1['done']);
        $this->assertEquals($user->id, $tarefa1['usuarioId']);

        $this->assertNotNull($tarefa2['id']);
        $this->assertEquals(strtotime("2022/08/27"), $tarefa2['data']);
        $this->assertEquals('Tarefa 2', $tarefa2['nome']);
        $this->assertEquals(true, $tarefa2['done']);
        $this->assertEquals($user->id, $tarefa2['usuarioId']);
    }
    
    public function test_find_all_sem_data_inicio() {
        $user = $this->saveUsuario();
        $this->saveTarefas($user);
        $token = $this->login('samuca@gmail.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->get('api/tarefas?fim=2022-08-31');

        $response->assertStatus(422);
    }

    public function test_find_all_sem_data_fim() {
        $user = $this->saveUsuario();
        $this->saveTarefas($user);
        $token = $this->login('samuca@gmail.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->get('api/tarefas?inicio=2022-08-31');

        $response->assertStatus(422);
    }

    public function test_find_all_sem_autenticacao() {
        $user = $this->saveUsuario();
        $this->saveTarefas($user);

        $response = $this->get('api/tarefas?inicio=2022-08-20&fim=2022-08-31');

        $response->assertStatus(401);
    }

    public function test_save_ok() {
        $user = $this->saveUsuario();
        $token = $this->login('samuca@gmail.com');

        $tarefa1 = new Tarefa;
        $tarefa1->data = strtotime("2022/08/26");
        $tarefa1->nome = 'Tarefa 1';
        $tarefa1->descricao = 'Descrição Tarefa 1';
        $tarefa1->done = false;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('api/tarefas', $tarefa1->toArray());

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereNot('id', null)
                ->where('data', strtotime("2022/08/26"))
                ->where('nome', 'Tarefa 1')
                ->where('descricao', 'Descrição Tarefa 1')
                ->where('done', false)
                ->etc()
        );            

    }

    public function test_save_sem_data() {
        $user = $this->saveUsuario();
        $token = $this->login('samuca@gmail.com');

        $tarefa1 = new Tarefa;
        $tarefa1->nome = 'Tarefa 1';
        $tarefa1->descricao = 'Descrição Tarefa 1';
        $tarefa1->done = false;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('api/tarefas', $tarefa1->toArray());

        $response->assertStatus(422);            
    }

    public function test_save_sem_nome() {
        $user = $this->saveUsuario();
        $token = $this->login('samuca@gmail.com');

        $tarefa1 = new Tarefa;
        $tarefa1->data = strtotime("2022/08/26");
        $tarefa1->descricao = 'Descrição Tarefa 1';
        $tarefa1->done = false;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('api/tarefas', $tarefa1->toArray());

        $response->assertStatus(422);            
    }

    public function test_save_sem_autenticacao() {
        $user = $this->saveUsuario();

        $tarefa1 = new Tarefa;
        $tarefa1->data = strtotime("2022/08/26");
        $tarefa1->nome = 'Tarefa 1';
        $tarefa1->descricao = 'Descrição Tarefa 1';
        $tarefa1->done = false;

        $response = $this->postJson('api/tarefas', $tarefa1->toArray());

        $response->assertStatus(401);
    }

    public function test_save_alteracao_ok() {
        $user = $this->saveUsuario();
        $token = $this->login('samuca@gmail.com');
        $t = $this->saveTarefa($user);

        $tarefa1 = new Tarefa;
        $tarefa1->id = $t->id;
        $tarefa1->data = strtotime("2022/08/28");
        $tarefa1->nome = 'Tarefa 1 alterada';
        $tarefa1->descricao = 'Descrição Tarefa 1 alterada';
        $tarefa1->done = true;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('api/tarefas', $tarefa1->toArray());

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
            $json->where('id', $t->id)
                ->where('data', strtotime("2022/08/28"))
                ->where('nome', 'Tarefa 1 alterada')
                ->where('descricao', 'Descrição Tarefa 1 alterada')
                ->where('done', true)
                ->etc()
        );            
    }

    /*
    public function test_save_alteracao_sem_data() {
        $usuario = $this->testeUtil->saveUsuario();
        $t = $this->saveTarefa($usuario);
        $token = $this->testeUtil->login('samuca@gmail.com');

        $tarefa1 = new Tarefa;
        $tarefa1->nome = 'Tarefa 1 alterada';
        $tarefa1->descricao = 'Descrição Tarefa 1 alterada';
        $tarefa1->done = true;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('api/tarefas', $tarefa1);

        $response->assertStatus(422);
    }

    public function test_save_alteracao_sem_nome() {
        $usuario = $this->testeUtil->saveUsuario();
        $t = $this->saveTarefa($usuario);
        $token = $this->testeUtil->login('samuca@gmail.com');

        $tarefa1 = new Tarefa;
        $tarefa1->data = strtotime("2022/08/28");
        $tarefa1->descricao = 'Descrição Tarefa 1 alterada';
        $tarefa1->done = true;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('api/tarefas', $tarefa1);

        $response->assertStatus(422);
    }

    public function test_save_alteracao_sem_autenticacao() {
        $usuario = $this->testeUtil->saveUsuario();
        $t = $this->saveTarefa($usuario);

        $tarefa1 = new Tarefa;
        $tarefa1->data = strtotime("2022/08/28");
        $tarefa1->descricao = 'Descrição Tarefa 1 alterada';
        $tarefa1->done = true;

        $response = $this->postJson('api/tarefas', $tarefa1);

        $response->assertStatus(401);
    }

    public function test_delete_ok() {
        $usuario = $this->testeUtil->saveUsuario();
        $t = $this->saveTarefa($usuario);
        $token = $this->testeUtil->login('samuca@gmail.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->delete('api/tarefas/' + $t->id);

        $response->assertStatus(200);
        $this->assertEquals('OK', $response->content()); 
    }

    public function test_delete_ok_sem_autenticacao() {
        $usuario = $this->testeUtil->saveUsuario();
        $t = $this->saveTarefa($usuario);

        $response = $this->delete('api/tarefas/' + $t->id);

        $response->assertStatus(401);
    }

    */
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

    private function saveTarefas(User $user) {
        $tarefa1 = new Tarefa;
        $tarefa1->data = strtotime("2022/08/26");
        $tarefa1->nome = 'Tarefa 1';
        $tarefa1->descricao = 'Descrição Tarefa 1';
        $tarefa1->done = false;
        $tarefa1->usuarioId = $user->id;

        $tarefa1->save();

        $tarefa2 = new Tarefa;
        $tarefa2->data = strtotime("2022/08/27");
        $tarefa2->nome = 'Tarefa 2';
        $tarefa2->done = true;
        $tarefa2->usuarioId = $user->id;

        $tarefa2->save();
    }

    private function saveTarefa(User $user): Tarefa {
        $tarefa1 = new Tarefa;
        $tarefa1->data = strtotime("2022/08/26");
        $tarefa1->nome = 'Tarefa 1';
        $tarefa1->descricao = 'Descrição Tarefa 1';
        $tarefa1->done = false;
        $tarefa1->usuarioId = $user->id;

        $tarefa1->save();

        return $tarefa1;
    }

}
