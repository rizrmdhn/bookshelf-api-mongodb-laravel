<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function show($id)
    {
        $book = Book::find($id);
        return  ResponseFormatter::success($book, 'Data buku berhasil diambil');
    }

    public function all()
    {
        $books = Book::all();
        return ResponseFormatter::success($books, 'Data buku berhasil diambil');
    }

    public function store(Request $request)
    {
        $books = new Book();

        $books->title = $request->title;
        $books->author = $request->author;
        $books->description = $request->description;

        $books->save();

        return ResponseFormatter::success($books, 'Data buku berhasil ditambahkan');
    }

    public function destroy($id)
    {
        $books = Book::find($id);
        $books->delete();

        return ResponseFormatter::success($books, 'Data buku berhasil dihapus');
    }
}
