<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\User;
use Validator;

class UserController extends Controller
{

    public function index()
    {
        $users = User::all();
        $data = $users->toArray();

        $response = [
            'success' => true,
            'data' => $data,
            'message' => 'Books retrieved successfully.'
        ];

        return response()->json($data, 200);
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 404);
        }

        $input = $request->all();
        unset($input['c_password']);
        $input['password'] = bcrypt($input['password']);

        if ($request->exists('id')) //update
            $user = User::findOrFail($request->input('id'));
        else //new
            $user = new User();
        
        $user->fill($input);
        $user->save();
        
        $user['token'] = $user->createToken('MyApp')->accessToken;

        return response()->json($user, 200);
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->accessToken;
            $success['id'] = $user->id;
            $success['name'] = $user->name;
            $success['email'] = $user->email;

            return response()->json($success, 200);
        } else {
            return response()->json('Unauthorised', 401);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user) {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 404);
        }

        $dados = $request->all();
        $dados['password'] = bcrypt($dados['password']);

        // buscando registro de número 1
        $user = User::find($dados['id']);
        // verificando se encontrou o user
        if ($user) {
            //atribuindo novos valores ou apenas alguns valores
            $user->fill($dados);
        }
        // salvando os dados passados
        $user->save();
        $data = $user->toArray();

        return response()->json($data, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $user = User::find($id);
        $data = $user->toArray();

        if (is_null($user)) {
            $response = [
                'success' => false,
                'data' => 'Empty',
                'message' => 'User not found.'
            ];
            return response()->json($response, 404);
        }
        return response()->json($data, 200);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        $data = $user->toArray();
        return response()->json($data, 200);
    }
}