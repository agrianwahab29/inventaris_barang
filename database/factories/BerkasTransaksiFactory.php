<?php

namespace Database\Factories;

use App\Models\BerkasTransaksi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

class BerkasTransaksiFactory extends Factory
{
    protected $model = BerkasTransaksi::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $fileNames = [
            'Surat_Serah_Terima_Barang_ATK_Q1_2026.pdf',
            'Dokumen_Permintaan_Pengadaan_Kertas_A4.pdf',
            'Berita_Acara_Serah_Terima_Stapler.pdf',
            'Tanda_Terima_Barang_Kantor_Bulan_Januari.pdf',
            'Surat_Permintaan_Pengadaan_Tinta_Printer.pdf',
            'Dokumen_Stock_Opname_Triwulan_I_2026.pdf',
            'Berita_Acara_Peminjaman_Kalkulator.pdf',
            'Surat_Jalan_Pengiriman_Barang_Ke_Ruangan_UKBI.pdf',
            'Tanda_Bukti_Penerimaan_Barang_Dari_Supplier.pdf',
            'Dokumen_Rekap_Transaksi_Barang_Masuk.pdf',
            'Surat_Perjanjian_Kerjasama_Pengadaan_ATK.pdf',
            'Berita_Acara_Penghapusan_Barang_Rusak.pdf',
            'Dokumen_Peminjaman_Gunting_Kertas_Untuk_Kegiatan.pdf',
            'Surat_Permintaan_Perbaikan_Stapler_Besar.pdf',
            'Tanda_Terima_Pengembalian_Barang_Dari_Ruangan_Alih_Daya.pdf',
            'Dokumen_Stock_Opname_Triwulan_II_2026.pdf',
            'Berita_Acara_Penerimaan_Barang_Dari_Gudang.pdf',
            'Surat_Permintaan_Pengadaan_Lem_Kertas_Bulan_Maret.pdf',
            'Dokumen_Peminjaman_Map_Folder_Untuk_Rapat.pdf',
            'Tanda_Bukti_Pengambilan_Barang_Oleh_Karyawan.pdf',
            'Surat_Serah_Terima_Barang_Kebersihan_Aqua_Botol.pdf',
            'Dokumen_Rekap_Penggunaan_Barang_Konsumsi.pdf',
            'Berita_Acara_Penghapusan_Keset_Kaki_Lama.pdf',
            'Surat_Permintaan_Pengadaan_Bolpoint_Biru.pdf',
            'Tanda_Terima_Barang_Dari_Vendor_Prima_Jaya.pdf',
            'Dokumen_Stock_Opname_Akhir_Tahun_2025.pdf',
            'Berita_Acara_Peminjaman_Penggaris_Dan_Penghapus.pdf',
            'Surat_Jalan_Pengiriman_Barang_Ke_Ruangan_Keuangan.pdf',
            'Dokumen_Permintaan_Pengadaan_Tinta_Printer_HP.pdf',
            'Tanda_Bukti_Penerimaan_Barang_Klip_Kertas.pdf',
            'Surat_Perjanjian_Pengadaan_Barang_ATK_Tahunan.pdf',
            'Berita_Acara_Serah_Terima_Staples_No_10.pdf',
            'Dokumen_Peminjaman_Tipe_X_Untuk_Pemeriksaan.pdf',
            'Surat_Permintaan_Perbaikan_Peralatan_Kantor.pdf',
            'Tanda_Terima_Pengembalian_Sisa_Barang_Proyek.pdf',
            'Dokumen_Stock_Opname_Triwulan_III_2026.pdf',
            'Berita_Acara_Penerimaan_Barang_Import_Dari_Jakarta.pdf',
            'Surat_Permintaan_Pengadaan_Kertas_Folio.pdf',
            'Dokumen_Peminjaman_Gunting_Besar_Untuk_Event.pdf',
            'Tanda_Bukti_Pengambilan_Stapler_Oleh_Bagian_HRD.pdf',
            'Surat_Serah_Terima_Barang_Konsumsi_Kopi_Dan_Gula.pdf',
            'Dokumen_Rekap_Transaksi_Barang_Keluar_Bulan_Februari.pdf',
            'Berita_Acara_Penghapusan_Barang_Elektronik_Rusak.pdf',
            'Surat_Permintaan_Pengadaan_Pensil_2B_Dan_Rautan.pdf',
            'Tanda_Terima_Barang_Dari_Supplier_Sinar_Mas.pdf',
            'Dokumen_Stock_Opname_Bulanan_Januari_2026.pdf',
            'Berita_Acara_Peminjaman_Kalkulator_Dan_Stapler.pdf',
            'Surat_Jalan_Pengiriman_Barang_Ke_Ruangan_SDM.pdf',
            'Dokumen_Permintaan_Pengadaan_Lem_Stick_Dan_Double_Tape.pdf',
            'Tanda_Bukti_Penerimaan_Barang_Pengepakan_Dari_Gudang.pdf',
            'Surat_Perjanjian_Pengadaan_Suku_Cadang_Printer.pdf',
            'Berita_Acara_Serah_Terima_Barang_Dari_Kepala_Bagian.pdf',
            'Dokumen_Peminjaman_Kertas_A4_Untuk_Pelatihan.pdf',
            'Surat_Permintaan_Perbaikan_Kursi_Dan_Meja_Rusak.pdf',
            'Tanda_Terima_Pengembalian_Barang_Dari_Karyawan_Resign.pdf',
            'Dokumen_Stock_Opname_Triwulan_IV_2026.pdf',
            'Berita_Acara_Penerimaan_Barang_Donasi_Dari_Pemerintah.pdf',
            'Surat_Permintaan_Pengadaan_Map_Folder_Warna.pdf',
            'Dokumen_Peminjaman_Peralatan_Kantor_Untuk_WFH.pdf',
            'Tanda_Bukti_Pengambilan_Tinta_Printer_Canon_Oleh_IT.pdf',
            'Surat_Serah_Terima_Barang_Habis_Pakai_Bulan_Maret.pdf',
            'Dokumen_Rekap_Penggunaan_ATK_Semester_I_2026.pdf',
            'Berita_Acara_Penghapusan_Archip_Lama_Dan_Tidak_Terpakai.pdf',
            'Surat_Permintaan_Pengadaan_Bolpoint_Hitam_Dan_Merah.pdf',
            'Tanda_Terima_Barang_Dari_Vendor_CV_Maju_Jaya.pdf',
            'Dokumen_Stock_Opname_Mid_Year_2026.pdf',
            'Berita_Acara_Peminjaman_Penghapus_Dan_Penggaris.pdf',
            'Surat_Jalan_Pengiriman_Barang_Ke_Ruangan_Publikasi.pdf',
            'Dokumen_Permintaan_Pengadaan_Kertas_A3_Untuk_Cetak.pdf',
            'Tanda_Bukti_Penerimaan_Barang_Konsumsi_Dari_Supplier.pdf',
            'Surat_Perjanjian_Kerjasama_Dengan_Toko_ATK_Nasional.pdf',
            'Berita_Acara_Serah_Terima_Gunting_Kecil_Batch_100.pdf',
            'Dokumen_Peminjaman_Stapler_Dan_Isi_Ulang.pdf',
            'Surat_Permintaan_Perbaikan_Mesin_Fotocopy_Rusak.pdf',
            'Tanda_Terima_Pengembalian_Sisa_Barang_Event_Nasional.pdf',
            'Dokumen_Stock_Opname_Triwulan_I_2027.pdf',
            'Berita_Acara_Penerimaan_Barang_Dari_Kantor_Pusat.pdf',
            'Surat_Permintaan_Pengadaan_Lakban_Dan_Plastic_Wrap.pdf',
            'Dokumen_Peminjaman_Meja_Lipat_Untuk_Rapat_Besar.pdf',
            'Tanda_Bukti_Pengambilan_Kertas_Folio_Oleh_Keuangan.pdf',
            'Surat_Serah_Terima_Barang_Inventaris_Bulanan.pdf',
            'Dokumen_Rekap_Transaksi_April_2026.pdf',
            'Berita_Acara_Penghapusan_Barang_Yang_Sudah_Habis_Masa_Pakai.pdf',
            'Surat_Permintaan_Pengadaan_Spanduk_Dan_Banner.pdf',
            'Tanda_Terima_Barang_Dari_Supplier_Toko_Buku_Nasional.pdf',
            'Dokumen_Stock_Opname_Final_Tahun_2026.pdf',
            'Berita_Acara_Peminjaman_Proyektor_Dan_Screen.pdf',
            'Surat_Jalan_Pengiriman_Barang_Ke_Cabang_Luar_Kota.pdf',
            'Dokumen_Permintaan_Pengadaan_Kertas_Sticker_Label.pdf',
            'Tanda_Bukti_Penerimaan_Barang_Dari_E_Commerce.pdf',
            'Surat_Perjanjian_Pengadaan_Barang_Dengan_Harga_Khusus.pdf',
            'Berita_Acara_Serah_Terima_Barang_Dari_Direktur.pdf',
            'Dokumen_Peminjaman_Kabel_HDMI_Dan_Adaptor.pdf',
            'Surat_Permintaan_Perbaikan_Jaringan_Internet_Rusak.pdf',
            'Tanda_Terima_Pengembalian_Barang_Dari_Staff_Magang.pdf',
            'Dokumen_Stock_Opname_Semester_II_2026.pdf',
            'Berita_Acara_Penerimaan_Barang_Sumbangan_Dari_Mitra.pdf',
            'Surat_Permintaan_Pengadaan_Toner_Printer_LaserJet.pdf',
            'Dokumen_Peminjaman_Peralatan_Presentasi_Lengkap.pdf',
            'Tanda_Bukti_Pengambilan_ATK_Oleh_Bagian_Umum.pdf',
        ];

        $perihalList = [
            'Serah terima barang ATK triwulan I',
            'Pengadaan kertas untuk keperluan kantor',
            'Peminjaman peralatan kantor',
            'Stock opname barang inventaris',
            'Pengembalian barang dari ruangan',
            'Permintaan perbaikan barang rusak',
            'Penghapusan barang tidak terpakai',
            'Pengadaan tinta printer untuk unit',
            'Serah terima barang dari supplier',
            'Rekap transaksi barang masuk/keluar',
            'Peminjaman barang untuk kegiatan rapat',
            'Pengembalian sisa barang proyek',
            'Pengadaan peralatan kantor baru',
            'Serah terima barang habis pakai',
            'Stock opname akhir tahun',
            'Penghapusan barang elektronik rusak',
            'Pengadaan suku cadang printer',
            'Peminjaman peralatan untuk WFH',
            'Serah terima barang dari vendor',
            'Rekap penggunaan ATK semesteran',
        ];

        $pengirimList = ['Gudang Pusat', 'Supplier Prima Jaya', 'Vendor Sinar Mas', 'Toko ATK Nasional', 'Kantor Cabang', 'Bagian Umum', 'Staff Gudang', 'Koordinator Logistik', 'Supplier Resmi', 'Vendor CV Maju Jaya'];
        $penerimaList = ['Ruangan UKBI', 'Ruangan Alih Daya', 'Bagian Keuangan', 'Bagian SDM', 'Bagian Umum', 'Staff Admin', 'Koordinator IT', 'Sekretariat', 'Ruang Rapat Utama', 'Ruang Publikasi'];
        
        $tanggalSurat = $this->faker->dateTimeBetween('2025-01-01', '2026-12-31');
        $fileSize = $this->faker->numberBetween(50000, 2000000); // 50KB - 2MB
        
        return [
            'nomor_surat' => $this->faker->optional(0.7)->regexify('[0-9]{3}/[A-Z]{2}/[A-Z]/[0-9]{4}'),
            'tanggal_surat' => $this->faker->optional(0.8)->dateTimeBetween('2025-01-01', '2026-12-31'),
            'perihal' => $this->faker->optional(0.75)->randomElement($perihalList),
            'pengirim' => $this->faker->optional(0.7)->randomElement($pengirimList),
            'penerima' => $this->faker->optional(0.7)->randomElement($penerimaList),
            'user_id' => User::factory(),
            'file_path' => 'berkas-transaksi/' . $this->faker->uuid . '.pdf',
            'file_name' => $this->faker->randomElement($fileNames),
            'file_size' => $fileSize,
            'file_mime' => 'application/pdf',
            'keterangan' => $this->faker->optional(0.3)->sentence(),
            'created_at' => $tanggalSurat,
            'updated_at' => $tanggalSurat,
        ];
    }
}
