<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Book;

class BookController extends Controller
{
    //
    public function index() {
        $books = Book::whereNotNull('description')->get();

        return response()->json([
            'message' => 'Books retrieved successfully',
            'data'    => [
                'books' => $books,
            ],
        ]);
    }
}
