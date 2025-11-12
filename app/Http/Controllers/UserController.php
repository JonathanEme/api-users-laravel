<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        // Requiere token (usa guard "api" configurado para JWT)
        $this->middleware('auth:api');
    }

    public function index()
    {
        // Devolver solo campos no sensibles
        $users = User::select('id','name','email','created_at','updated_at')->get();
        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        // Solo el propio usuario puede ver sus detalles (ajusta si quieres permitir a otros usuarios autenticados)
        if (auth()->id() !== $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // Responder solo con campos seguros
        $data = $user->only(['id','name','email','created_at','updated_at']);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Solo el propio usuario puede actualizar su cuenta
        if (auth()->id() !== $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        $user->save();

        // Responder solo con campos seguros
        $data = $user->only(['id','name','email','created_at','updated_at']);
        return response()->json($data);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Solo el propio usuario puede eliminar su cuenta
        if (auth()->id() !== $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $user->delete();
        return response()->json(null, 204);
    }
}
