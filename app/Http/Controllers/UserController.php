<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Models\User;
use App\Mail\ResetPassword;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function __construct()
    {
        $this->classe = User::class;
    }

    public function index(Request $request)
    {
        return $this->classe::paginate($request->per_page);
    }

    public function login(Request $request)
    {
        // CRIA REGRA
        // $role = Role::create(['name' => 'teste']);
        // $role = Role::create(['name' => 'admin']);
        // $role = Role::create(['name' => 'user']);

        // CRIA PERMISSAO
        // $permission = Permission::create(['name' => 'get']);
        // $permission = Permission::create(['name' => 'put']);
        // $permission = Permission::create(['name' => 'post']);
        // $permission = Permission::create(['name' => 'delete']);
        // $permission = Permission::create(['name' => 'teste']);

        // VINCULA REGRA E PERMISSSAO
        // $role = Role::findById(1);
        // $permission = Permission::findById(3);
        // $role->givePermissionTo(1, 2, 3);

        // CRIA PERMISSAO
        // $permission = Permission::create(['name' => 'edit post']);

        // ADICIONA PERMISSAO AO USUARIO
        // $user = User::find(1); //id usuario
        // $user->givePermissionTo('get'); //nome da regra
        // $user->givePermissionTo('put'); //nome da regra
        // $user->givePermissionTo('post'); //nome da regra

        // ADICIONA REGRAS AO USUARIO
        $user = User::find(1); //id usuario
        $user->assignRole('super-admin'); //nome da permissao

        // retorna regras de um usuario
        // $user = User::find(1);
        // return $user->getAllPermissions();

        $rules = array(
            'email' => 'required|email',
            'password' => 'required|min:5',
        );
        $messages = array(
            'required' => ':attribute é obrigatorio.',
            'email.email' => ':attribute é invlálido.',
            'password.min' => ':attribute precisa ter o minimo de 5 caracteres.',
        );
        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 409);
        }

        $user = User::where('email', $request->email)->first();

        if (
            is_null($user)
            || !Hash::check($request->password, $user->password)
        ) {
            return response()->json(
                [
                    'error' => 'Usuário ou senha inválidos'
                ],
                401
            );
        }

        $data = $request->only('email', 'password');

        $token = JWTAuth::attempt($data);

        !empty($token['token']) ? $this->removeRememberToken($user->id) : null;

        return $this->responseToken($token);
    }

    private function responseToken($token)
    {
        return $token ? ['token' => "Bearer $token"] : response()->json([
            'error' => 'Credenciais inválidas'
        ], 400);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        JWTAuth::parseToken()->invalidate();

        return ['success' => 'Logout realizado com sucesso'];
    }

    public function store(Request $request)
    {
        $rules = array(
            'name' => 'required|min:6',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|confirmed',
            'role' => 'required|min:1',
        );
        $messages = array(
            'required' => ':attribute é obrigatorio.',
            'name.min' => ':attribute precisa ter o minimo de 6 caracteres.',
            'email.email' => ':attribute é invlálido.',
            'email.unique' => 'Este email já esta cadastrado.',
            'password.min' => ':attribute precisa ter o minimo de 5 caracteres.',
            'password.confirmed' => 'A confirmação da senha não corresponde'
        );
        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(
                ['error' => $validator->errors()->first()],
                409
            );
        }

        $user = User::create(
            $request->only('email', 'name', 'password')
        );
        $user->syncRoles($request->role);

        return response()->json(
            ['success' => 'Usuario adicionado com sucesso.'],
            201
        );
    }

    public function update(int $id, Request $request)
    {
        $recurso =  User::find($id);

        if (is_null($recurso)) {
            return response()->json(['erro' => 'Recurso não encontrado'], 404);
        }

        $recurso->fill($request->only('email', 'name', 'password', 'role'));
        $recurso->save();

        return response()->json($recurso);
    }

    public function permission(int $id, Request $request)
    {
        $user = User::find($id);
        $user->syncRoles($request->role);

        $user = User::find($id);
        return $user->getAllPermissions();
    }

    public function resetPasswordEmail(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            array('email' => 'required|email'),
            array('email.email' => ':attribute é invlálido.')
        );
        if ($validator->fails()) {
            return response()->json(
                ['error' => $validator->errors()->first()],
                409
            );
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $remember_code = rand(100000, 999999);
            $user->remember_code = $remember_code;
            $user->created_remember_code = date('Y-m-d H:i:s');
            if (!$user->save()) {
                return response()->json(
                    ['error' => 'Ocorreu um erro na geração do remember token']
                );
            }

            if (!is_null($user['email'])) {
                Mail::to($user['email'])
                    ->send(new ResetPassword($user['name'], $remember_code));
            }
        }

        return response()->json(
            [
                'success' =>
                'Acesse seu email e siga as instruções para recuperar a senha.'
            ]
        );
    }

    public function validateRememberCode(Request $request)
    {
        $data = $request->only('email', 'code');

        $user = User::where('email', $data['email'])
            ->where('remember_code', $data['code'])->first();

        if (!is_null($user)) {
            $hoursSince = Helpers::getDateDiference($user['created_remember_code']);

            if ($hoursSince >= 5) {
                return Response(["error" => 'Este link não é mais válido'], 412);
            } else {
                return Response(["success" => 'Código validado com sucesso']);
            }
        } else {
            return Response(["error" => 'Este link não é mais válido'], 412);
        }
    }

    public function resetPasswordCode(Request $request)
    {
        $data = $request->only('email', 'password', 'code');

        $checkUser = User::where('email', $data['email'])
            ->where('remember_code', $data['code'])->first();

        if (empty($checkUser)) {
            return Response(
                [
                    "error" => 'Ocorreu um erro, por favor refaça o processo'
                ],
                412
            );
        }

        $rules = array(
            'email' => 'required|email|min:10',
            'code' => 'required|min:6|max:6',
            'password' => 'required|min:5|confirmed'
        );
        $messages = array(
            'required' => ':attribute é obrigatorio.',
            'email.min' => ':attribute precisa ter o minimo de 10 caracteres.',
            'code.min' => ':attribute precisa ter o minimo de 6 caracteres.',
            'code.max' => ':attribute precisa ter o minimo de 6 caracteres.',
            'password.min' => ':attribute precisa ter o minimo de 5 caracteres.',
            'password.confirmed' => 'A confirmação da senha não corresponde'
        );
        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(
                ['error' => $validator->errors()->first()],
                409
            );
        }

        $update = $this->removeRememberToken($checkUser['id']);

        if ($update) {
            return response()->json(
                ['success' => 'Sua senha foi atualizada com sucesso']
            );
        }

        return response()->json(
            ['error' => 'Ocorreu um erro ao realizar a atualização'],
            409
        );
    }

    private function removeRememberToken($id)
    {
        $data['remember_code'] = null;
        $data['created_remember_code']  = null;
        return User::find($id)->update($data);
    }
}
