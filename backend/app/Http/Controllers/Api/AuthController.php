<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', //tidak boleh duplicate di table users
            'password' => 'required|string|min:8|confirmed', //confirmed = harus ada field:password_confirmation
            'role' => 'sometimes|in:buyer,seller',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
            'password.confirmed' => 'Konfirmasi password tidak cocok'
        ]);

        //Insert ke database table users
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']), //Password di-hash, tidak disimpan plaintext
            'role' => $validated['role'] ?? 'buyer',
            'balance' => 0,
        ]);

        //Generate Token (Sanctum) -> return object
        $token = $user->createToken('auth_token')->plainTextToken; //buat token yang mengembalikan object kemudian get property (string token) dari object itu

        return response()->json([
            'success' => true,
            'message' => 'Registrasi Berhasil',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer', //Siapa pun yang membawa token ini = dianggap valid user (Tanpa password lagi)
            ]
        ], 201);
    }

    public function login(Request $request): JsonResponse {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Email wajib diisi',
            'password.required' => 'Password wajib diisi'
        ]);

        $user = User::where('email', $validated['email'])->first();

        //user tidak ditemukan & (ambil password dari input (plain text) lalu bandingkan dengan password di DB (hash))
        if(!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        //Generate Token (Sanctum) -> return object
        //1. Token tanpa abilities : akses penuh (default), sama dengan full-access
        $token = $user->createToken('auth_token')->plainTextToken; //buat token yang mengembalikan object kemudian get property (string token) dari object itu

        //2. Token dengan abilities
        //akses full token
        //$token = $user->createToken('full-access')->plainTextToken;

        //akses read only
        //$token = $user->createToken('read-only', ['read'])->plainTextToken;

        //3. Token granular (lebih detail)
        //$token = $user->createToken('product-manage', ['product:create', 'product:update'])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login Berhasil',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer', //standar HTTP authentication -> Siapa pun yang membawa token ini = dianggap valid user (Tanpa password lagi)
            ]
        ], 201);
    }

    public function logout(Request $request): JsonResponse {
        //1. $request->user() : Ambil user yang sedang login
        // Background : dari header request Laravel (via Sanctum) ambil token -> cek ke database -> kalau valid Laravel tahu itu milik user siapa -> inject ke $request->user()
        //2. currentAccessToken() : Ambil token yang sedang dipakai dari header request tsb
        //3. delete() : Hapus token itu dari database
        $request->user()->currentAccessToken()->delete(); // hapus semua token user

        //hapus token tertentu
        //$request->user()->where('id', $tokenId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout Berhasil'
        ]);
    }

    public function me(Request $request): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => 'data user',
            'data' => $request->user() //Ambil user yang sedang login
        ]);
    }
}
