<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CloudinaryStorage;
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
        $validator = Validator::make($request->all(), [
            'image'   => 'required|image|mimes:jpeg,png,jpg',
            'user_id' => 'required|integer|exists:users,id',
            'deskripsi' => 'nullable|string'
        ]);

      
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        
        $image  = $request->file('image');
        $result = CloudinaryStorage::upload($image->getRealPath(), $image->getClientOriginalName());

        
        $gambar = Gambar::create([
            'gambarUrl' => $result,
            'user_id'   => $request->user_id,
            'deskripsi' => $request->deskripsi,
        ]);

        // Return response
        return new GambarResource(true, 'Gambar berhasil ditambahkan!', $gambar);
    }


    public function update(Request $request, $id)
    {
        // Find the existing Gambar record
        $gambar = Gambar::findOrFail($id);

        // Define validation rules
        $validator = Validator::make($request->all(), [
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
            'user_id'   => 'required|integer|exists:users,id',
            'deskripsi' => 'nullable|string'
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        // If new image is uploaded, delete old one and upload new
        if ($request->hasFile('image')) {
            // Delete old image from Cloudinary
            CloudinaryStorage::delete($gambar->gambarUrl);

            // Upload new image to Cloudinary
            $image  = $request->file('image');
            $result = CloudinaryStorage::upload($image->getRealPath(), $image->getClientOriginalName());

            // Update gambarUrl with new image URL
            $gambar->gambarUrl = $result;
        }

        // Update other fields
        $gambar->user_id   = $request->user_id;
        $gambar->deskripsi = $request->deskripsi;
        $gambar->save();

        // Return response
        return new GambarResource(true, 'Gambar berhasil diperbarui!', $gambar);
    }

    public function destroy($id)
    {
        // Find the existing Gambar record
        $gambar = Gambar::findOrFail($id);

        // Delete image from Cloudinary
        CloudinaryStorage::delete($gambar->gambarUrl);

        // Delete the record from the database
        $gambar->delete();

        // Return response
        return response()->json(['success' => true, 'message' => 'Gambar berhasil dihapus']);
    }
}