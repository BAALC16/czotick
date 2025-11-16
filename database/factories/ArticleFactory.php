<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->paragraph(1),
                'chapo' => $this->faker->paragraph(3),
                'content' => implode($this->faker->paragraphs(10)),
                'user_id'=>1,
        ];
    }
}
