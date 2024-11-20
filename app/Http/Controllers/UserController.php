<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth; 
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
            'saldo' => 'required|numeric' 
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
            'role' => $request->input('role'),
            'saldo' => $request->input('saldo') 
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'User registered successfully!',
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
            'data' => $user,
            'token' => $token,
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

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'npm' => 'sometimes|required|string',
            'no_telepon' => 'sometimes|required|string',
            'email' => 'sometimes|required|string|email|max:100|unique:users,email,' . $id,
            'name' => 'sometimes|required|string',
            'password' => 'sometimes|required|string|min:6',
            'role' => 'sometimes|required|string|in:Mahasiswa,Staff,Dosen',
            'saldo' => 'sometimes|required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->messages()
            ], 400);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->npm = $request->input('npm', $user->npm);
        $user->no_telepon = $request->input('no_telepon', $user->no_telepon);
        $user->email = $request->input('email', $user->email);
        $user->name = $request->input('name', $user->name);
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->role = $request->input('role', $user->role);
        $user->saldo = $request->input('saldo', $user->saldo);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ], 200);
    }   
}


