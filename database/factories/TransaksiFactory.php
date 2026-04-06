<?php

namespace Database\Factories;

use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransaksiFactory extends Factory
{
    protected $model = Transaksi::class;

    public function definition()
    {
        $tipe = $this->faker->randomElement(['masuk', 'keluar', 'masuk_keluar']);
        $jumlah = $this->faker->numberBetween(1, 50);
        $stokSebelum = $this->faker->numberBetween(20, 100);

        $jumlahMasuk = 0;
        $jumlahKeluar = 0;
        $stokSetelahMasuk = $stokSebelum;
        $sisaStok = $stokSebelum;

        if ($tipe === 'masuk') {
            $jumlahMasuk = $jumlah;
            $stokSetelahMasuk = $stokSebelum + $jumlah;
            $sisaStok = $stokSetelahMasuk;
        } elseif ($tipe === 'keluar') {
            $jumlahKeluar = $jumlah;
            $sisaStok = $stokSebelum - $jumlah;
        } elseif ($tipe === 'masuk_keluar') {
            $jumlahMasuk = $jumlah;
            $jumlahKeluar = $this->faker->numberBetween(1, $jumlah);
            $stokSetelahMasuk = $stokSebelum + $jumlah;
            $sisaStok = $stokSetelahMasuk - $jumlahKeluar;
        }

        return [
            'barang_id' => Barang::factory(),
            'tipe' => $tipe,
            'jumlah' => $jumlah,
            'jumlah_masuk' => $jumlahMasuk,
            'jumlah_keluar' => $jumlahKeluar,
            'stok_sebelum' => $stokSebelum,
            'stok_setelah_masuk' => $stokSetelahMasuk,
            'sisa_stok' => $sisaStok,
            'tanggal' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'tanggal_keluar' => $tipe !== 'masuk' ? $this->faker->dateTimeBetween('-1 year', 'now') : null,
            'ruangan_id' => Ruangan::factory(),
            'user_id' => User::factory(),
            'nama_pengambil' => $tipe !== 'masuk' ? $this->faker->name() : null,
            'tipe_pengambil' => $tipe !== 'masuk' ? $this->faker->randomElement(['nama_ruangan', 'ruangan_saja']) : null,
            'keterangan' => $this->faker->optional()->sentence(),
        ];
    }

    public function masuk()
    {
        return $this->state(function (array $attributes) {
            $jumlah = $this->faker->numberBetween(1, 50);
            $stokSebelum = $this->faker->numberBetween(20, 100);
            
            return [
                'tipe' => 'masuk',
                'jumlah' => $jumlah,
                'jumlah_masuk' => $jumlah,
                'jumlah_keluar' => 0,
                'stok_sebelum' => $stokSebelum,
                'stok_setelah_masuk' => $stokSebelum + $jumlah,
                'sisa_stok' => $stokSebelum + $jumlah,
                'tanggal_keluar' => null,
                'nama_pengambil' => null,
                'tipe_pengambil' => null,
            ];
        });
    }

    public function keluar()
    {
        return $this->state(function (array $attributes) {
            $jumlah = $this->faker->numberBetween(1, 20);
            $stokSebelum = $this->faker->numberBetween(30, 100);
            
            return [
                'tipe' => 'keluar',
                'jumlah' => $jumlah,
                'jumlah_masuk' => 0,
                'jumlah_keluar' => $jumlah,
                'stok_sebelum' => $stokSebelum,
                'stok_setelah_masuk' => $stokSebelum,
                'sisa_stok' => $stokSebelum - $jumlah,
            ];
        });
    }
}
