<?php

namespace App\Http\Controllers;

use App\Dao\UserDao;
use App\Models\User;
use Illuminate\Http\Request;

class UserRestController extends Controller {

    private UserDao $userDao;

    public function __construct() {
        $this->userDao = new UserDao;
        $this->middleware('auth:api');
    }

    public function save(Request $request) {

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = auth()->user();
        $user->id = $user->_id;

        $user->name = $request->name;
        $user->email = $request->email;

        $password = bcrypt($request->password);
        $user->password = $password;

        $user = $this->userDao->save($user);
        $user->password = null;

        return response()->json($user, 200);
    }    

    public function delete() {
        $user = auth()->user();
        $user->id = $user->_id;

        if (!$user) {
            return response()->json(['message' => 'Não existe usuario logado'], 422);
        }

        $this->userDao->delete($user);
        return response('OK', 200);
    }    
}
