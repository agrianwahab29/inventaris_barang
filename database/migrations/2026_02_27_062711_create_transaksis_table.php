<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->string('tipe')->default('masuk');
            $table->integer('jumlah_masuk')->default(0);
            $table->integer('jumlah_keluar')->default(0);
            $table->integer('jumlah')->default(0);
            $table->integer('stok_sebelum')->default(0);
            $table->integer('stok_setelah_masuk')->default(0);
            $table->integer('sisa_stok')->default(0);
            $table->date('tanggal');
            $table->foreignId('ruangan_id')->nullable()->constrained('ruangans')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_pengambil')->nullable();
            $table->enum('tipe_pengambil', ['nama_ruangan', 'ruangan_saja'])->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksis');
    }
}
