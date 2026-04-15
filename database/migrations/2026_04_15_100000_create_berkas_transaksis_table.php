<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBerkasTransaksisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('berkas_transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->nullable()->comment('Nomor surat serah terima');
            $table->date('tanggal_surat')->nullable()->comment('Tanggal surat');
            $table->string('perihal')->nullable()->comment('Perihal/keperluan dokumen');
            $table->string('pengirim')->nullable()->comment('Pihak yang menyerahkan');
            $table->string('penerima')->nullable()->comment('Pihak yang menerima');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict')->comment('User yang upload');
            $table->string('file_path')->comment('Path file relatif terhadap storage');
            $table->index('file_path');
            $table->string('file_name')->comment('Nama asli file');
            $table->string('file_size')->nullable()->comment('Ukuran file');
            $table->string('file_mime')->comment('MIME type file'); // No default
            $table->text('keterangan')->nullable()->comment('Keterangan tambahan');
            $table->timestamps();
            
            // Index untuk pencarian
            $table->index('nomor_surat');
            $table->index('tanggal_surat');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('berkas_transaksis');
    }
}
