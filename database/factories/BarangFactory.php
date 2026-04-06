<?php

namespace Database\Factories;

use App\Models\Barang;
use Illuminate\Database\Eloquent\Factories\Factory;

class BarangFactory extends Factory
{
    protected $model = Barang::class;

    public function definition()
    {
        return [
            'nama_barang' => $this->faker->words(3, true),
            'kategori' => $this->faker->randomElement(['ATK', 'Kebersihan', 'Konsumsi', 'Perlengkapan', 'Lainnya']),
            'satuan' => $this->faker->randomElement(['unit', 'pcs', 'set', 'box']),
            'stok' => $this->faker->numberBetween(0, 100),
            'stok_minimum' => $this->faker->numberBetween(5, 20),
            'catatan' => $this->faker->optional()->sentence(),
        ];
    }

    public function lowStock()
    {
        return $this->state(function (array $attributes) {
            return [
                'stok' => 3,
                'stok_minimum' => 10,
            ];
        });
    }

    public function emptyStock()
    {
        return $this->state(function (array $attributes) {
            return [
                'stok' => 0,
                'stok_minimum' => 10,
            ];
        });
    }
}
