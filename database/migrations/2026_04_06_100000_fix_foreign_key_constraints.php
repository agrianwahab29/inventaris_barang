<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixForeignKeyConstraints extends Migration
{
    /**
     * Run the migrations.
     *
     * Fix CASCADE DELETE to RESTRICT to prevent accidental data loss.
     * When a barang/user is deleted, transaction history should be preserved.
     *
     * @return void
     */
    public function up()
    {
        // Skip on SQLite - it doesn't support dropping foreign keys
        // This migration is only needed for MySQL production
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Fix transaksis table - barang_id
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropForeign(['barang_id']);
            
            $table->foreign('barang_id')
                ->references('id')
                ->on('barangs')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });

        // Fix transaksis table - user_id
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });

        // Fix transaksis table - ruangan_id
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropForeign(['ruangan_id']);
            
            $table->foreign('ruangan_id')
                ->references('id')
                ->on('ruangans')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });

        // Fix quarterly_stock_opnames table - barang_id
        Schema::table('quarterly_stock_opnames', function (Blueprint $table) {
            $table->dropForeign(['barang_id']);
            
            $table->foreign('barang_id')
                ->references('id')
                ->on('barangs')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });

        // Fix quarterly_stock_opnames table - user_id
        Schema::table('quarterly_stock_opnames', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Skip on SQLite - it doesn't support dropping foreign keys
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Revert transaksis - barang_id to CASCADE
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropForeign(['barang_id']);
            $table->foreign('barang_id')
                ->references('id')
                ->on('barangs')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        // Revert transaksis - user_id to CASCADE
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        // Revert transaksis - ruangan_id
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropForeign(['ruangan_id']);
            $table->foreign('ruangan_id')
                ->references('id')
                ->on('ruangans')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        // Revert quarterly_stock_opnames - barang_id
        Schema::table('quarterly_stock_opnames', function (Blueprint $table) {
            $table->dropForeign(['barang_id']);
            $table->foreign('barang_id')
                ->references('id')
                ->on('barangs')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        // Revert quarterly_stock_opnames - user_id
        Schema::table('quarterly_stock_opnames', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }
}
