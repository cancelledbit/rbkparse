<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Article;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ShowNews extends Controller
{
    public function index() {
        $articles = Article::query()->orderBy('created_at', 'desc')->paginate(15);
        return view('home', ['articles' => $articles]);
    }

    public function show(string $id) {
        try {
            $article = Article::query()->findOrFail($id);
            return view('article', ['article' => $article]);
        } catch (ModelNotFoundException $e) {
            abort(404);
        }
    }
}
