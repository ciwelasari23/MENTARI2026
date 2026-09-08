<?php

namespace Database\Factories;

use App\Models\MstWilayah;
use Illuminate\Database\Eloquent\Factories\Factory;

class MstWilayahFactory extends Factory
{
    protected $model = MstWilayah::class;

    public function definition(): array
    {
        return [
            'id_wilayah' => (string) $this->faker->unique()->numberBetween(1400, 1499),
            'nama_wilayah' => strtoupper($this->faker->city),
            'level_wilayah' => 2,
        ];
    }
}