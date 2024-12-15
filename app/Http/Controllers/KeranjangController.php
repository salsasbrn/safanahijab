<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Details;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KeranjangController extends Controller
{
    public function index()
    {
        // Pastikan pengguna sudah login
        $user = Auth::user();
        if (!$user || $user->role !== 'customer') {
            return back()->with('error', 'You are not authorized to access this page.');
        }

        // Keranjang akan diambil dari localStorage di sisi klien
        return view('customer.keranjang');
    }

    public function add(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Ambil produk dari database
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Produk tidak ditemukan.'], 404);
        }

        // Produk akan ditambahkan di sisi klien menggunakan JavaScript
        $productData = [
            "id" => $product->id,
            "name" => $product->name,
            "quantity" => $request->quantity,
            "price" => $product->price,
            "image" => $product->image,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang.',
            'product' => $productData
        ]);
    }

    public function update(Request $request, $key)
    {
        // Validasi input
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Update akan dilakukan di sisi klien menggunakan JavaScript
        return response()->json([
            'success' => true,
            'message' => 'Jumlah produk berhasil diperbarui.'
        ]);
    }

    public function destroy($key)
    {
        // Penghapusan akan dilakukan di sisi klien menggunakan JavaScript
        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus dari keranjang.'
        ]);
    }

    public function buy(Request $request)
    {
        // Pastikan pengguna sudah login
        $user = Auth::user();
        if (!$user || $user->role !== 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login untuk melakukan pembelian.'
            ], 401);
        }

        // Terima data keranjang dari request
        $keranjang = $request->input('keranjang', []);

        if (empty($keranjang)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang Anda kosong.'
            ], 400);
        }

        try {
            // Mulai transaksi database
            DB::beginTransaction();

            // Buat order baru
            $order = new Order();
            $order->user_id = $user->id;

            // Hitung total dengan validasi harga
            $total = 0;
            $orderDetails = [];

            foreach ($keranjang as $item) {
                // Validasi produk dari database
                $product = Product::find($item['id']);

                if (!$product) {
                    throw new \Exception("Produk dengan ID {$item['id']} tidak ditemukan.");
                }

                // Pastikan harga sesuai dengan harga aktual produk
                if ($product->price != $item['price']) {
                    throw new \Exception("Harga produk {$product->name} telah berubah.");
                }

                // Validasi stok
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok produk {$product->name} tidak mencukupi.");
                }

                // Hitung total dan simpan detail
                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $orderDetails[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price
                ];

                // Kurangi stok produk
                $product->stock -= $item['quantity'];
                $product->save();
            }

            // Simpan order
            $order->total = $total;
            $order->status = 'pending';
            $order->save();

            // Simpan detail pesanan
            $detailsToInsert = array_map(function ($detail) use ($order, $user) {
                return [
                    'order_id' => $order->id,
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'price' => $detail['price'],
                    'user_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }, $orderDetails);

            Details::insert($detailsToInsert);

            // Commit transaksi
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembelian berhasil dilakukan.',
                'order_id' => $order->id
            ]);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function cart()
    {
        return view('customer.keranjang');
    }
}
