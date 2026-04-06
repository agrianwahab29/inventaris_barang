<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCriticalIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * Add indexes to frequently queried columns for 10-100x performance improvement.
     * 
     * @return void
     */
    public function up()
    {
        // Barangs table indexes
        Schema::table('barangs', function (Blueprint $table) {
            // Index for category filtering
            $table->index('kategori', 'barangs_kategori_index');
            
            // Index for stock queries (low stock alerts)
            $table->index('stok', 'barangs_stok_index');
            
            // Index for name search
            $table->index('nama_barang', 'barangs_nama_index');
            
            // Composite index for dashboard queries
            $table->index(['kategori', 'stok'], 'barangs_kategori_stok_index');
        });

        // Transaksis table indexes
        Schema::table('transaksis', function (Blueprint $table) {
            // Index for date filtering (reports)
            $table->index('tanggal', 'transaksis_tanggal_index');
            
            // Index for transaction type filtering
            $table->index('tipe', 'transaksis_tipe_index');
            
            // Index for barang relationship
            $table->index('barang_id', 'transaksis_barang_id_index');
            
            // Index for user relationship
            $table->index('user_id', 'transaksis_user_id_index');
            
            // Index for ruangan relationship
            $table->index('ruangan_id', 'transaksis_ruangan_id_index');
            
            // Composite indexes for common queries
            $table->index(['tipe', 'tanggal'], 'transaksis_tipe_tanggal_index');
            $table->index(['barang_id', 'tipe'], 'transaksis_barang_tipe_index');
            $table->index(['tanggal', 'tipe'], 'transaksis_tanggal_tipe_index');
        });

        // Quarterly_stock_opnames table indexes
        Schema::table('quarterly_stock_opnames', function (Blueprint $table) {
            // Index for quarter filtering
            $table->index('quarter', 'quarterly_stock_opnames_quarter_index');
            
            // Index for year filtering
            $table->index('year', 'quarterly_stock_opnames_year_index');
            
            // Index for barang relationship
            $table->index('barang_id', 'quarterly_stock_opnames_barang_id_index');
            
            // Index for user relationship
            $table->index('user_id', 'quarterly_stock_opnames_user_id_index');
            
            // Composite index for quarterly reports
            $table->index(['year', 'quarter'], 'quarterly_stock_opnames_year_quarter_index');
        });

        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            // Index for role filtering
            $table->index('role', 'users_role_index');
            
            // Index for username search (unique already, but explicit index)
            $table->index('username', 'users_username_index');
        });

        // Ruangans table indexes
        Schema::table('ruangans', function (Blueprint $table) {
            // Index for name search
            $table->index('nama_ruangan', 'ruangans_nama_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop barangs indexes
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropIndex('barangs_kategori_index');
            $table->dropIndex('barangs_stok_index');
            $table->dropIndex('barangs_nama_index');
            $table->dropIndex('barangs_kategori_stok_index');
        });

        // Drop transaksis indexes
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropIndex('transaksis_tanggal_index');
            $table->dropIndex('transaksis_tipe_index');
            $table->dropIndex('transaksis_barang_id_index');
            $table->dropIndex('transaksis_user_id_index');
            $table->dropIndex('transaksis_ruangan_id_index');
            $table->dropIndex('transaksis_tipe_tanggal_index');
            $table->dropIndex('transaksis_barang_tipe_index');
            $table->dropIndex('transaksis_tanggal_tipe_index');
        });

        // Drop quarterly_stock_opnames indexes
        Schema::table('quarterly_stock_opnames', function (Blueprint $table) {
            $table->dropIndex('quarterly_stock_opnames_quarter_index');
            $table->dropIndex('quarterly_stock_opnames_year_index');
            $table->dropIndex('quarterly_stock_opnames_barang_id_index');
            $table->dropIndex('quarterly_stock_opnames_user_id_index');
            $table->dropIndex('quarterly_stock_opnames_year_quarter_index');
        });

        // Drop users indexes
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_index');
            $table->dropIndex('users_username_index');
        });

        // Drop ruangans indexes
        Schema::table('ruangans', function (Blueprint $table) {
            $table->dropIndex('ruangans_nama_index');
        });
    }
}
