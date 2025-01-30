<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Validator;

class AuthController extends Controller
{

    public function register(StoreUserProfileRequest $request)
    {
        // Los datos ya están validados gracias a la Request Class

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Encriptar la contraseña
        ]);

        $user->profile()->create([
            'ci' => $request->ci,
            'lastName' => $request->lastName,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        $user->assignRole('user'); // Asigna el rol 'user' por defecto

        return response()->json(['message' => 'Usuario registrado correctamente', 'user' => $user], 201); // Código 201 Created
    }

    public function prueba(){
        return response()->json(['message' => 'prueba'], 200);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // public function login()
    // {       


    //     $credentials = request(['email', 'password']);

    //     if (! $token = auth()->attempt($credentials)) {
    //         return response()->json(['error' => 'Credenciales Incorrectas'], 401);
    //     }

    //     return $this->respondWithToken($token);
    // }

    public function login(Request $request)
    {
        // 1. Validar los datos de la petición
        $validator = FacadesValidator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [ // Mensajes de error personalizados (opcional pero recomendado)
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        // 2. Verificar si la validación falla
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // Retorna 422 con los errores
        }

        // 3. Obtener las credenciales (SOLO después de la validación)
        $credentials = $request->only(['email', 'password']);

        // 4. Intentar la autenticación
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Credenciales Incorrectas'], 401);
        }

        // 5. Devolver la respuesta con el token
        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(new UserResource(auth()->user()));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $user = auth()->user();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => new UserResource($user),
            'roles' => $user->getRoleNames(), // Obtiene los nombres de los roles
            // 'permissions' => $user->getPermissionsViaRoles(), // Obtiene los permisos a través de los roles
        ]);
    }
}
