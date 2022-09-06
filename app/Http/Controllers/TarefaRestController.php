<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Dao\TarefaDao;
use App\Models\Tarefa;

class TarefaRestController extends Controller {
    private TarefaDao $tarefaDao;

    public function __construct() {
        $this->tarefaDao = new TarefaDao;
        $this->middleware('auth:api');
    }

    public function findAll(Request $request) {

        //valida o periodo
        $inicio = $request->query('inicio');
        $fim = $request->query('fim');

        if (!$inicio || !$fim) {
            return response()->json('os parâmetros inicio e fim devem ser preenchidos', 422);
        }

        $user = auth()->user();
        $user->id = $user->_id;

        $inicioDate = strtotime($inicio);
        $fimDate = strtotime($fim);
        $tarefaList = $this->tarefaDao->findAllByDataBetweenAndUsuarioId($inicioDate, $fimDate, $user->id);

        return response()->json($tarefaList, 200);
    }    

    public function save(Request $request) {

        //valida o request
        $validated = $request->validate([
            'data' => 'required',
            'nome' => 'required'
        ]);        

        $user = auth()->user();
        $user->id = $user->_id;

        $tarefa = new Tarefa;
        if ($request->id) {
            $tarefa = $this->tarefaDao->get($request->id);
        }
        
        $tarefa->usuarioId = $user->id;

        $tarefa->data = $request->data;
        $tarefa->nome = $request->nome;
        $tarefa->descricao = $request->descricao;
        $tarefa->done = $request->done;

        $tarefa = $this->tarefaDao->save($tarefa);
        
        return response()->json($tarefa, 200);
    }    

    public function delete(string $id) {
        $tarefa = $this->tarefaDao->get($id);
        $this->tarefaDao->delete($tarefa);

        return response('OK', 200);
    }    
}
