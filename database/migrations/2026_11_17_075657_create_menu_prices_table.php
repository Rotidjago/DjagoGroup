<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Riwayat harga dan pembagian per menu (berlaku untuk semua cabang).
 * Semua nilai dalam rupiah bulat per pcs. Harga yang dipakai untuk sebuah
 * penjualan adalah baris dengan effective_from terbaru yang <= tanggal penjualan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->restrictOnDelete();
            $table->date('effective_from');
            $table->unsignedInteger('price');            // Harga
            $table->unsignedInteger('hpp');              // HPP
            $table->unsignedInteger('seller_share');     // Bagi Hasil Penjual
            $table->unsignedInteger('partner_share');    // Bagi Hasil Partner (investor)
            $table->unsignedInteger('debt_installment'); // Cicil Hutang
            $table->unsignedInteger('coupon_budget');    // Budget Kupon
            $table->timestamps();

            $table->unique(['menu_id', 'effective_from']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_prices');
    }
};
