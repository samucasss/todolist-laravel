<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Dao\PreferenciasDao;
use App\Models\Preferencias;

class PreferenciasRestController extends Controller {
    private PreferenciasDao $preferenciasDao;

    public function __construct() {
        $this->preferenciasDao = new PreferenciasDao;
        $this->middleware('auth:api');
    }

    public function get() {
        $preferencias = $this->findPreferenciasUsuarioLogado();
        return response()->json($preferencias, 200);
    }    

    public function save(Request $request) {
        $request->validate([
            'tipoFiltro' => 'required|string',
            'done' => 'required|boolean'
        ]);        

        $preferencias = new Preferencias;

        $preferenciasExistente = $this->findPreferenciasUsuarioLogado();

        if ($preferenciasExistente) {
            $preferencias = $this->preferenciasDao->get($preferenciasExistente->id);
        }

        $preferencias->tipoFiltro = $request->tipoFiltro;
        $preferencias->done = $request->done;

        $user = auth()->user();
        $user->id = $user->_id;
        $preferencias->usuarioId = $user->id;

        $preferencias = $this->preferenciasDao->save($preferencias);
        
        return response()->json($preferencias, 200);
    }    

    public function delete() {
        $preferencias = $this->findPreferenciasUsuarioLogado();

        if (!$preferencias) {
            return response()->json(['message' => 'Não existe preferencias para usuario logado'], 422);
        }

        return response('OK', 200);
    }    

    private function findPreferenciasUsuarioLogado() {
        $user = auth()->user();
        $user->id = $user->_id;

        $preferencias = $this->preferenciasDao->findByUsuarioId($user->id);
        return $preferencias;
    }
}
