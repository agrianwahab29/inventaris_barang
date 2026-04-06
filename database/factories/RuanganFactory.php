<?php

namespace Database\Factories;

use App\Models\Ruangan;
use Illuminate\Database\Eloquent\Factories\Factory;

class RuanganFactory extends Factory
{
    protected $model = Ruangan::class;

    public function definition()
    {
        return [
            'nama_ruangan' => $this->faker->randomElement([
                'Ruang Meeting A',
                'Ruang Meeting B',
                'Ruang Direktur',
                'Ruang Staff',
                'Gudang',
                'Ruang IT',
            ]),
            'keterangan' => $this->faker->optional()->sentence(),
        ];
    }
}
