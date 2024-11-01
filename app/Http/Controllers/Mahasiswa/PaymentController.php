<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'price' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->messages()
            ], 400);
        }

        $user = User::find($request->input('user_id'));

        if ($user->saldo < $request->input('price')) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo tidak cukup'
            ], 400);
        }

          // Mulai transaksi
        DB::beginTransaction();
        try {
            // Kurangi saldo pengguna
            $user->saldo -= $request->input('price');
            $user->save();

            // Buat entri pembayaran baru
            $payment = Payment::create([
                'invoice_number' => uniqid('INV-'),
                'price' => $request->input('price'),
                'status' => 'success',
                'payment_method' => $request->input('payment_method'),
                'qr_url' => $this->generateQrUrl($request->input('invoice_number')),
                'user_id' => $user->id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil',
                'payment' => $payment
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function generateQrUrl($invoiceNumber)
    {
        return "https://api.qrserver.com/v1/create-qr-code/?data=" . urlencode($invoiceNumber);
    }
}