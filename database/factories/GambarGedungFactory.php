<?php

namespace Database\Factories;

use App\Models\GambarGedung;
use Illuminate\Database\Eloquent\Factories\Factory;

class GambarGedungFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GambarGedung::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'gedung_id' => $this->faker->word,
        'nama_file' => $this->faker->word,
        'path_foto' => $this->faker->word,
        'keterangan' => $this->faker->word,
        'urutan' => $this->faker->randomDigitNotNull,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
