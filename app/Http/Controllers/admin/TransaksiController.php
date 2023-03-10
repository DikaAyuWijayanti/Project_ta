<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Order;

class TransaksiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // memanggil fungsi notif dari helpers.php
        $notifikasi = notifAdmin();
        //ambil data order yang status nya 1 atau masih baru/belum melalukan pembayaran
        $order = DB::table('order')
            ->join('status_order', 'status_order.id', '=', 'order.status_order_id')
            ->join('users', 'users.id', '=', 'order.user_id')
            ->select('order.*', 'status_order.name', 'users.name as nama_pemesan')
            ->where('order.status_order_id', 1)
            ->get();
        $data = array(
            'orderbaru' => $order,
            'notif' => $notifikasi,
            'totalPem' => $notifikasi[4]
        );

        return view('admin.transaksi.index', $data);
    }

    public function detail($id)
    {
        // memanggil fungsi notif dari helpers.php
        $notifikasi = notifAdmin();
        //ambil data detail order sesuai id
        $detail_order = DB::table('detail_order')
            ->join('products', 'products.id', '=', 'detail_order.product_id')
            ->join('order', 'order.id', '=', 'detail_order.order_id')
            ->select('products.name as nama_produk', 'products.image', 'detail_order.*', 'products.price', 'order.*')
            ->where('detail_order.order_id', $id)
            ->get();
        $order = DB::table('order')
            ->join('users', 'users.id', '=', 'order.user_id')
            ->join('status_order', 'status_order.id', '=', 'order.status_order_id')
            ->select('order.*', 'users.name as nama_pelanggan', 'status_order.name as status')
            ->where('order.id', $id)
            ->first();
        $data = array(
            'detail' => $detail_order,
            'order'  => $order,
            'notif' => $notifikasi,
            'totalPem' => $notifikasi[4]
        );
        return view('admin.transaksi.detail', $data);
    }

    public function perludicek()
    {
        // memanggil fungsi notif dari helpers.php
        $notifikasi = notifAdmin();
        //ambil data order yang status nya 2 atau belum di cek / sudah bayar
        $order = DB::table('order')
            ->join('status_order', 'status_order.id', '=', 'order.status_order_id')
            ->join('users', 'users.id', '=', 'order.user_id')
            ->select('order.*', 'status_order.name', 'users.name as nama_pemesan')
            ->where('order.status_order_id', 2)
            ->orWhere('order.status_order_id', 3)
            ->get();
        $data = array(
            'orderbaru' => $order,
            'notif' => $notifikasi,
            'totalPem' => $notifikasi[4]
        );

        return view('admin.transaksi.perludicek', $data);
    }

    public function perludikirim()
    {
        // memanggil fungsi notif dari helpers.php
        $notifikasi = notifAdmin();
        //ambil data order yang status nya 4 sudah dicek dan perlu dikirim(yang sudah di input no resi)
        $order = DB::table('order')
            ->join('status_order', 'status_order.id', '=', 'order.status_order_id')
            ->join('users', 'users.id', '=', 'order.user_id')
            ->select('order.*', 'status_order.name', 'users.name as nama_pemesan')
            ->where('order.status_order_id', 4)
            ->get();
        $data = array(
            'orderbaru' => $order,
            'notif' => $notifikasi,
            'totalPem' => $notifikasi[4]
        );

        return view('admin.transaksi.perludikirim', $data);
    }

    public function selesai()
    {
        // memanggil fungsi notif dari helpers.php
        $notifikasi = notifAdmin();
        //ambil data order yang status nya 6 barang sudah diterima pelangan
        $order = DB::table('order')
            ->join('status_order', 'status_order.id', '=', 'order.status_order_id')
            ->join('users', 'users.id', '=', 'order.user_id')
            ->select('order.*', 'status_order.name', 'users.name as nama_pemesan')
            ->where('order.status_order_id', 6)
            ->get();
        $data = array(
            'orderbaru' => $order,
            'notif' => $notifikasi,
            'totalPem' => $notifikasi[4]
        );

        return view('admin.transaksi.selesai', $data);
    }

    public function dibatalkan()
    {
        // memanggil fungsi notif dari helpers.php
        $notifikasi = notifAdmin();
        //ambil data order yang status nya 7 dibatalkan pelanngan
        $order = DB::table('order')
            ->join('status_order', 'status_order.id', '=', 'order.status_order_id')
            ->join('users', 'users.id', '=', 'order.user_id')
            ->select('order.*', 'status_order.name', 'users.name as nama_pemesan')
            ->where('order.status_order_id', 7)
            ->get();
        $data = array(
            'orderbaru' => $order,
            'notif' => $notifikasi,
            'totalPem' => $notifikasi[4]
        );

        return view('admin.transaksi.dibatalkan', $data);
    }

    public function dikirim()
    {
        // memanggil fungsi notif dari helpers.php
        $notifikasi = notifAdmin();
        //ambil data order yang status nya 5 atau sedang dikirim
        $order = DB::table('order')
            ->join('status_order', 'status_order.id', '=', 'order.status_order_id')
            ->join('users', 'users.id', '=', 'order.user_id')
            ->select('order.*', 'status_order.name', 'users.name as nama_pemesan')
            ->where('order.status_order_id', 5)
            ->get();
        $data = array(
            'orderbaru' => $order,
            'notif' => $notifikasi,
            'totalPem' => $notifikasi[4]
        );

        return view('admin.transaksi.dikirim', $data);
    }

    public function konfirmasi($id)
    {
        //function ini untuk mengkonfirmasi bahwa pelanngan sudah melakukan pembayaran
        $order = Order::findOrFail($id);
        $order->status_order_id = 4;
        $order->save();

        $kurangistok = DB::table('detail_order')->where('order_id', $id)->get();
        foreach ($kurangistok as $kurang) {
            $ambilproduk = DB::table('products')->where('id', $kurang->product_id)->first();
            $ubahstok = $ambilproduk->stok - $kurang->qty;

            $update = DB::table('products')
                ->where('id', $kurang->product_id)
                ->update([
                    'stok' => $ubahstok
                ]);
        }
        return redirect()->route('admin.transaksi.perludikirim')->with('status', 'Berhasil Mengonfirmasi Pembayaran Pesanan');
    }

}
