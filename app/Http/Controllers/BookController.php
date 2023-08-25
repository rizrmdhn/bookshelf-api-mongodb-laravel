<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Mongodb\Query\Builder;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function show($id)
    {
        $userId = auth()->guard('api')->user()->id;

        $book = Book::where('_id', $id)->where('ownerId', $userId)->first();

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
        $params = request()->input('search');

        if ($params) {
            $books = Book::where('title', 'like', '%' . $params . '%')
                ->orWhere('author', 'like', '%' . $params . '%')
                ->orWhere('description', 'like', '%' . $params . '%')
                ->orWhere('publisher', 'like', '%' . $params . '%')
                ->get();
        } else {
            $books = new Book();

            $books = $books->paginate(10);
        }

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

    public function updateBook(Request $request, $id)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'title' => 'required|string',
                    'author' => 'required|string',
                    'description' => 'required|string',
                    'publisher' => 'required|string',
                    'cover' => 'image|mimes:jpeg,png,jpg|max:2048'
                ],
                [
                    'title.required' => 'Please fill your title',
                    'author.required' => 'Please fill your author',
                    'description.required' => 'Please fill your description',
                    'publisher.required' => 'Please fill your publisher',
                    'cover.image' => 'Cover must be an image',
                    'cover.mimes' => 'Cover must be jpeg, png, or jpg',
                    'cover.max' => 'Cover max size is 2MB'
                ]
            );

            if ($validator->fails()) {
                return ResponseFormatter::error(
                    null,
                    $validator->errors(),
                    400
                );
            }

            $userId = auth()->guard('api')->user()->id;

            $books = Book::where('_id', $id)->where('ownerId', $userId)->first();

            if (!$books) {
                return ResponseFormatter::error(
                    null,
                    'Anda tidak memiliki buku ini',
                    404
                );
            }

            $oldBookCover = Book::where('_id', $id)->where('ownerId', $userId)->first()->cover;

            if ($oldBookCover) {
                Storage::delete('public/cover/' . $oldBookCover);
            }

            $books->title = $request->title;
            $books->author = $request->author;
            $books->description = $request->description;
            $books->publisher = $request->publisher;

            if ($request->file('cover')) {
                $cover = $request->file('cover')->store('cover', 'public');
                $books->cover = $request->file('cover')->hashName();
                $books->cover_url = env('APP_URL') . '/storage/' . $cover;
            }


            $books->save();

            return ResponseFormatter::success($books, 'Data buku berhasil diupdate');
        } catch (\Throwable $error) {
            return ResponseFormatter::error(null, $error->getMessage(), 500);
        }
    }
}
