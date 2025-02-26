<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Product::class;

    public function definition()
    {
        return [
            'product_name' => $this->faker->word,
            'slug'         => $this->faker->slug,
            'user_id'      => '1',
            'category_id'  => $this->faker->numberBetween(1, 2),
            'brand_id'     => $this->faker->numberBetween(1, 2),
            'size_id'      => $this->faker->numberBetween(1, 2),
            'material_id'  => $this->faker->numberBetween(1, 2),
            'color_id'     => $this->faker->numberBetween(1, 2),
            'condition_id' => $this->faker->numberBetween(1, 2),
            'suitable_id'  => $this->faker->numberBetween(1, 2),
            'description'  => $this->faker->text,
            'cloth_type'   => $this->faker->randomElement(['new','old']),
            'stock'        => $this->faker->numberBetween(1, 100),
            'price'        => $this->faker->randomFloat(2, 10, 500),
            'image'        => $this->faker->imageUrl(),
            'status'       => $this->faker->randomElement(['active', 'inactive']),
            'created_at'   => now(),
            'updated_at'   => now(),
        ];
    }
}
