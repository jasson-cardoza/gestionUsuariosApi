<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;




class UserController extends Controller
{
    //

    public function register(Request $request)
    {
        // Logic for user registration

        try{
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);
            
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password) // se puede usar bycrypt o Hash::make
            ]);

            return response()->json([
                'message' => 'Usuario registrado exitosamente',
                'user' => $user
            ], 201);


        }catch(Exception $error){
            return response()->json([
                'message' => 'Error al registrar el usuario',
                'error' => $error->getMessage()
            ], 500);

        }
    }

    public function login(Request $request)
    {
        try{

            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string|min:8',
            ]);

            $credentials = $request->only('email', 'password'); // ponemos el only por si el usuario manda mas datos, solo tomamos el mail y password
            
            //intentar autenticar al usuario
            //si las credenciales estan bien, se genera el token de acceso]

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'error' => 'Credenciales inválidas'
                ],401);
            }

                return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => '5 minutos'//JWTAuth::factory()->getTTL() * 60
                ]);



        }catch(Exception $error){
            return response()->json([
                'message' => 'error al iniciar sesión',
                'error' => $error->getMessage()
            ],500);
        }
        
    }

    public function index(){

        try{
            // $usuarios = DB::table('users')->select('name', 'email')->get();
            $usersEloquent = user::all('name', 'email');
            
            return response()->json([
                'data' => $usersEloquent
            ]);
        }
        
        catch(Exception $error){
            return response()->json([
                'message' => 'Error al obtener los usuarios',
                'error' => $error->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, string $id){
        try{

            //buscamos el usuario por su ID
            $users = User::findOrFail($id);

            // validamos el que sea el mismo usuario 
                 if($users->id !== $request->user()->id){
                return response()->json([
                    'error'=> 'Unaunthorized'
                ]);
            }
            
            
            $users->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password) 
            ]);
            
            return response()->json([
                'message' => 'Usuario actualizado exitosamente',
                'user' => $users
            ]);
        }
        
        catch(Exception $error){
            return response()->json([
                'message' => 'Error al obtener el usuario',
                'error' => $error->getMessage()
            ], 500);
        }
    }

     public function destroy(Request $request,string $id)
    {
        try{
            //buscamos el post por su ID
            $users = User::findOrFail($id);

            // validar que el post sea del usuario
            if($users->id !== $request->user()->id){
                return response()->json([
                    'error'=> 'Unaunthorized'
                ]);
            };
            
            //Eliminamos los usuarios con el metodo delete()
            $users->delete();
            return response()->json([
                'message' => 'Post eliminado con exito'
            ]);
        }
         catch(Exception $error){
            return response()->json([
                'error' => $error->getMessage()
            ]);
        }
        

    }

     //estadisticas de usuarios registrados por dia, semana y mes.
    public function estadisticas(){
        //usuarios registrados por dia
        $usuariosPorDia = User::select(DB::raw('YEAR(created_at) as fecha'), DB::raw('COUNT(*) as total'))
        ->groupBy('fecha')
        ->orderBy('fecha', 'desc')
        ->get();

        $usuariosPorSemana = User::select(DB::raw('YEAR(created_at) as año'), DB::raw('WEEK(created_at, 1) as semana'), DB::raw('COUNT(*) as total'))
        ->groupBy('año', 'semana')
        ->orderBy('año', 'desc')
        ->orderBy('semana', 'desc')
        ->get();

        $usuariosPorMes = User::select(DB::raw('YEAR(created_at) as año'), DB::raw('MONTH(created_at) as mes'), DB::raw('COUNT(*) as total'))
        ->groupBy('año', 'mes')
        ->orderBy('año', 'desc')
        ->orderBy('mes', 'desc')
        ->get();

        return response()->json([
            'message' => 'Estadisticas De Usuarios Registrados',
            'diario' => $usuariosPorDia,
            'semanal' => $usuariosPorSemana,
            'mensual' => $usuariosPorMes           
        ]);
        
    }


public function bladeIndex()
    {
        $users = User::all();
        return view('users', compact('users'));
    }

    public function bladeCreate()
    {
        return view('form');
    }

    public function bladeEdit(User $user)
    {
        return view('form', compact('user'));
    }
}