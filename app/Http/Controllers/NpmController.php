<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa; // Import model Mahasiswa
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NpmController extends Controller
{
    public function checkNpm($npm)
    {
        // Validasi format NPM, misalnya harus angka dan memiliki panjang tertentu (misal 8 digit)
        $validator = Validator::make(['npm' => $npm], [
            'npm' => 'required'

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'NPM tidak valid. Harus berupa angka 8 digit.'
            ], 400);
        }

        // Mencari mahasiswa berdasarkan NPM di database
        $user = User::where('npm', $npm)->first();

        if ($user) {
            return response()->json([
                'status' => 'success',
                'npm' => $user->npm,
                'no_telepon' => $user->no_telepon,
                'email'=> $user->email,
                'name' => $user->name,
                'role' => $user->role,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'NPM tidak ditemukan.'
            ], 404);
        }
    }
}
