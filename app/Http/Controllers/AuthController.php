<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Dao\UserDao;

class AuthController extends Controller {

    private UserDao $userDao;

    public function __construct() {
        $this->userDao = new UserDao;
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = new User;

        $user->name = $request->name;
        $user->email = $request->email;

        $password = bcrypt($request->senha);
        $user->password = $password;

        $user = $this->userDao->save($user);
        $user->password = null;

        return response()->json($user, 200);
    }

    public function login() {
        $credentials = request(['email', 'password']);
        $token = auth()->attempt($credentials);

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json(['token' => $token], 200);
    }

    public function get() {
        $user = auth()->user();
        $user->id = $user->_id;
        unset($user->_id);
        $user->password = null;

        return response()->json($user);
    }

}