<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
            $table->enum('kategori', ['ATK', 'Kebersihan', 'Konsumsi', 'Perlengkapan', 'Lainnya'])->default('Lainnya');
            $table->enum('satuan', ['Buah', 'Rim', 'Dos', 'Lusin', 'Pak', 'Box', 'Galon', 'Botol', 'Bungkus', 'Kilo', 'Pasang', 'Warna', 'Jenis', 'Kotak', 'Gantung', 'Lembar'])->default('Buah');
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(5);
            $table->text('catatan')->nullable();
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
        Schema::dropIfExists('barangs');
    }
}
