<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Customer yang melakukan pembayaran
            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            // Paket yang digunakan saat tagihan dibuat
            $table->foreignId('package_id')
                ->constrained('packages')
                ->restrictOnDelete();

            // Periode tagihan
            $table->string('periode');

            // Tahun tagihan
            $table->year('tahun');

            // Jumlah tagihan
            $table->decimal('jumlah_tagihan', 12, 2);

            // Status pembayaran
            $table->enum('status', ['belum_bayar', 'lunas'])
                ->default('belum_bayar');

            // Tanggal customer melakukan pembayaran
            $table->date('tanggal_bayar')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};