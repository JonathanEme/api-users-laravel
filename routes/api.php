<?php
// Nota: Se añadió README.md y .gitignore en la raíz del proyecto.
// Sigue README.md para pasos de subida a GitHub y configuración local.

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Use the Laravel auth guard middleware (auth:api) so the framework guard handles auth
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Public: debug route to inspect incoming headers and auth status (use this to test your PUT)
Route::get('debug-auth', function (Request $request) {
    return response()->json([
        'auth_user'  => auth('api')->user(),       // null si no hay token válido
        'authorized' => auth('api')->check(),
        'headers'    => $request->header(),        // inspecciona Authorization y demás headers
    ]);
});

// Note: when calling PUT /api/posts/{id} ensure header:
// Authorization: Bearer <token>
// Accept: application/json

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('miusuario', [AuthController::class, 'miusuario']);

    // Añadido: logout y refresh de JWT
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    // Users
    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::patch('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);

    // Posts
    Route::get('posts', [PostController::class, 'index']);
    Route::post('posts', [PostController::class, 'store']);
    Route::get('posts/{id}', [PostController::class, 'show']);
    Route::put('posts/{id}', [PostController::class, 'update']);
    Route::patch('posts/{id}', [PostController::class, 'update']);
    Route::delete('posts/{id}', [PostController::class, 'destroy']);
});
