<?php

namespace App\Http\Controllers\Api;

use App\Models\Gambar;
use App\Models\Post;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\GambarResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        try {

            $posts = Gambar::latest()->paginate(5);
            return new GambarResource(true, 'List Data Posts', $posts);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data post',
            ], 500);
        }
    }
    public function show($id)
    {
        try {
            $post = Gambar::findOrFail($id);
            return new GambarResource(true, 'Detail Data Post', $post);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post dengan ID ' . $id . ' tidak ditemukan',
            ], 500);
        }
    }
    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif',
            'user_id'     => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        //create post
        $post = Gambar::create([
            'gambarUrl'     => $image->hashName(),
        ]);

        //return response
        return new GambarResource(true, 'Data Post Berhasil Ditambahkan!', $post);
    }


    public function update(Request $request, $id)
    {
        $post = Gambar::findOrFail($id);
        $post->update($request->all());
        return new GambarResource(true, 'Post Updated', $post);
    }
    public function destroy($id)
    {
        $post = Gambar::findOrFail($id);
        $post->delete();
        return new GambarResource(true, 'Post Deleted', null);
    }
}