<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeSatuanToString extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // SQLite tidak mendukung ALTER COLUMN, jadi buat table baru
        Schema::create('barangs_new', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
            $table->enum('kategori', ['ATK', 'Kebersihan', 'Konsumsi', 'Perlengkapan', 'Lainnya'])->default('Lainnya');
            $table->string('satuan', 50)->default('Buah'); // Ubah dari enum ke string
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(5);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // Copy data dari table lama
        DB::statement('INSERT INTO barangs_new SELECT * FROM barangs');

        // Drop table lama
        Schema::drop('barangs');

        // Rename table baru
        Schema::rename('barangs_new', 'barangs');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Tidak bisa rollback ke enum setelah jadi string
        // Biarkan tetap string karena backward compatible
    }
}
