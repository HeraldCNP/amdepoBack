<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserProfileRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Validator;

class AuthController extends Controller
{

    public function index()
    {
        $usuarios = User::with('profile')->orderBy('id', 'desc')->paginate(10); // Eager loading
        return UserResource::collection($usuarios); // Usa el recurso para la colección
    }


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

        if ($request->has('roles')) { // Si usas roles
            $user->syncRoles($request->roles);
        }

        return response()->json(['message' => 'Usuario registrado correctamente', 'user' => $user], 201); // Código 201 Created
    }

    public function prueba()
    {
        return response()->json(['message' => 'prueba'], 200);
    }

    public function show($id)
    {     


        $user = User::with('profile', 'roles')->find($id); // Usa find() en lugar de findOrFail()

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404); // Devuelve 404 si no se encuentra el usuario
        }

        // Autorización: Solo el mismo usuario o un admin puede ver los datos
        if (auth()->user()->id != $id && !auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return new UserResource($user);
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserProfileRequest $request, $id)
    {

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        $user->profile()->update([ // Usa update directamente
            'ci' => $request->ci,
            'lastName' => $request->lastName,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        if ($request->has('roles')) { // Si usas roles
            $user->syncRoles($request->roles);
        }

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado con éxito',
            'data' => new UserResource($user) // Opcional: incluye los datos actualizados del usuario
        ], 200);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Autorización: Solo el mismo usuario o un admin puede eliminar
        if (auth()->user()->id != $id && !auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Eliminar perfil relacionado
        if ($user->profile) {
            $user->profile()->delete();
        }

        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }



    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

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

    public function searchUsers(Request $request)
    {
        // dd($request->all());
        $filtro = $request->input('filtro'); // Obtén el término de búsqueda del usuario

        $usuarios = User::where('name', 'like', "%$filtro%") // Busca por nombre
            ->orWhereHas('profile', function ($query) use ($filtro) {
                $query->where('ci', 'like', "%$filtro%"); // Busca por CI en el perfil
            })
            ->with('profile') // Incluye los datos del perfil en la respuesta
            ->get();

        // dd($usuarios);
        return response()->json($usuarios);
    }
}
