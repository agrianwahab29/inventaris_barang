<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuarterlyStockOpnamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quarterly_stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->integer('tahun');
            $table->enum('quarter', ['Q1', 'Q2', 'Q3', 'Q4']);
            $table->integer('stok_opname');
            $table->date('tanggal_opname');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Unique constraint: one stock opname per barang per quarter per year
            $table->unique(['barang_id', 'tahun', 'quarter']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quarterly_stock_opnames');
    }
}
