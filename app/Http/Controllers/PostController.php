<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return response()->json(Post::with('user:id,name,email')->get());
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);
        if ($v->fails()) return response()->json($v->errors(), 422);

        try {
            $post = auth()->user()->posts()->create($request->only(['title','body']));
            return response()->json($post, 201);
        } catch (QueryException $e) {
            $msg = $e->getMessage();
            if (str_contains(strtolower($msg), 'unknown column') || str_contains(strtolower($msg), 'column not found')) {
                return response()->json([
                    'error' => 'Database schema mismatch: missing column (por ejemplo: body).',
                    'detail' => 'Ejecute las migraciones: php artisan migrate',
                    'exception' => $msg,
                ], 500);
            }
            return response()->json(['error' => 'Database error', 'exception' => $msg], 500);
        }
    }

    public function show($id)
    {
        $post = Post::with('user:id,name,email')->findOrFail($id);
        return response()->json($post);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        if ($post->user_id !== auth()->id()) return response()->json(['error'=>'Forbidden'], 403);

        $v = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'body' => 'sometimes|string',
        ]);
        if ($v->fails()) return response()->json($v->errors(), 422);

        $post->update($request->only(['title','body']));
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        if ($post->user_id !== auth()->id()) return response()->json(['error'=>'Forbidden'], 403);
        $post->delete();
        return response()->json(null, 204);
    }
}
