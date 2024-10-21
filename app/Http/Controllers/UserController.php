<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth; // Pastikan ini diimport
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'npm' => 'required|string',
            'no_telepon' => 'required|string',
            'email' => 'required|string|email|max:100|unique:users',
            'name' => 'required|string',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:Mahasiswa,Staff,Dosen',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->messages()
            ], 400);
        }

        $user = User::create([
            'npm' => $request->input('npm'),
            'no_telepon' => $request->input('no_telepon'),
            'email' => $request->input('email'),
            'name' => $request->input('name'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sukses register',
            'data' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->messages()
            ], 400);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Login gagal, cek email dan password Anda'
            ], 401);
        }

        $user = Auth::user();

        return response()->json([
            'success' => true,
            'message' => 'Login sukses',
            'token' => $token,
            'data' => $user
        ], 200);
    }


        public function getUser()
        {
            try {
                if (!$user = JWTAuth::parseToken()->authenticate()) {
                    return response()->json(['message' => 'User tidak ditemukan'], 404);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil Mengambil Data User',
                    'user' => $user
                ], 200);

            } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                return response()->json(['message' => 'Token sudah kadaluarsa'], 401);
            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                return response()->json(['message' => 'Token tidak valid'], 401);
            } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
                return response()->json(['message' => 'Token tidak ditemukan'], 401);
            }
        }
    }


