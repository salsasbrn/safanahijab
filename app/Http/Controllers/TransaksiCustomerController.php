<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TransaksiCustomerController extends Controller
{
    public function index(Request $request)
    {
        // Pastikan pengguna sudah login
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        // Periksa apakah pengguna adalah 'customer'
        if ($user->role !== 'customer') {
            return back()->with('error', 'You are not authorized to access this page.');
        }

        // Ambil status dari query string (konversikan ke lowercase untuk konsistensi)
        $status = strtolower($request->input('status'));
        $searchId = $request->input('search_id');

        // Ambil semua order untuk customer, dengan filter berdasarkan status dan ID jika ada
        $query = Order::where('user_id', $user->id);

        // Filter berdasarkan status
        if ($status && $status !== 'all') {
            $query->where('status', ucfirst($status)); // Pastikan huruf pertama kapital
        }

        // Filter berdasarkan ID
        if ($searchId) {
            $query->where('id', $searchId);
        }

        $orders = $query->latest()->get(); // Urutkan dari yang terbaru

        // Kembalikan view dengan data order
        return view('customer.transaksi', compact('orders'));
    }

    public function getOrderDetail($id)
    {
        try {
            // Pastikan order milik user yang sedang login
            $order = Order::where('id', $id)
                ->where('user_id', Auth::id())
                ->with(['details' => function ($query) {
                    $query->with('product');
                }])
                ->firstOrFail();

            // Transformasi data untuk menghindari encoding issues
            $details = $order->details->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'product' => [
                        'id' => $detail->product->id,
                        'name' => $detail->product->name ?? 'Produk Tidak Diketahui'
                    ],
                    'quantity' => $detail->quantity,
                    'price' => $detail->price
                ];
            });

            return response()->json([
                'details' => $details,
                'total' => $order->total
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            Log::error('Order Detail Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Order tidak ditemukan',
                'message' => $e->getMessage()
            ], 404);
        }
    }
}
