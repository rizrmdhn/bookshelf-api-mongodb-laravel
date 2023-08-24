<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function show($id)
    {
        $userId = auth()->guard('api')->user()->id;

        $book = Book::where('id', $id)->where('ownerId', $userId)->first();

        if (!$book) {
            return ResponseFormatter::error(
                null,
                'Anda tidak memiliki buku ini',
                404
            );
        }

        return  ResponseFormatter::success($book, 'Data buku berhasil diambil');
    }

    public function all()
    {
        $books = Book::all();
        return ResponseFormatter::success($books, 'Data buku berhasil diambil');
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required|string',
                'author' => 'required|string',
                'description' => 'required|string',
                'publisher' => 'required|string'
            ],
            [
                'title.required' => 'Please fill your title',
                'author.required' => 'Please fill your author',
                'description.required' => 'Please fill your description',
                'publisher.required' => 'Please fill your publisher'
            ],
        );

        if ($validator->fails()) {
            return ResponseFormatter::error(
                null,
                $validator->errors(),
                400
            );
        }

        $books = new Book();

        $books->title = $request->title;
        $books->author = $request->author;
        $books->description = $request->description;
        $books->publisher = $request->publisher;
        $books->ownerId = auth()->guard('api')->user()->id;

        $books->save();

        return ResponseFormatter::success($books, 'Data buku berhasil ditambahkan');
    }

    public function destroy($id)
    {
        $userId = auth()->guard('api')->user()->id;

        $books = Book::where('id', $id)->where('ownerId', $userId)->first();

        if (!$books) {
            return ResponseFormatter::error(
                null,
                'Anda tidak memiliki buku ini',
                404
            );
        }

        $books->delete();

        return ResponseFormatter::success($books, 'Data buku berhasil dihapus');
    }
}
