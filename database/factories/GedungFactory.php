<?php

namespace Database\Factories;

use App\Models\Gedung;
use Illuminate\Database\Eloquent\Factories\Factory;

class GedungFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Gedung::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nama_gedung' => $this->faker->word,
        'alamat' => $this->faker->text,
        'deskripsi' => $this->faker->text,
        'fungsi' => $this->faker->word,
        'jumlah_lantai' => $this->faker->randomDigitNotNull,
        'tahun_berdiri' => $this->faker->randomDigitNotNull,
        'kondisi' => $this->faker->word,
        'x' => $this->faker->word,
        'y' => $this->faker->word,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
