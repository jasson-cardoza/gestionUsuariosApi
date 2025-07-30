<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\error;

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
            //si las credenciales estan bien, se genera el token de acceso
            if (Auth::attempt($credentials)){  // se puede obetener usuario desde auth o se puede obtener directamente del request

                $user = $request->user(); // se guardan las credenciales en la variable user

                //Aquí se genera el token de acceso

                $token = $user->createToken('auth_token')->plainTextToken; // se genera el token de acceso

                return response()->json([
                    'message' => 'Inicio de sesión exitoso',
                    'user' => $user,
                    'token' => $token
                ],200);
            }


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
}

