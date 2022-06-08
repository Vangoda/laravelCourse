<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        /** @var Product $product */
        $product = Product::inRandomOrder()->first();
        $quantity = $this->faker->numberBetween(1, 10);

        return [
            'product_title' => $product->title,
            'price' => $product->price,
            'quantity' => $quantity,
            'admin_revenue' => 0.9 * $quantity * $product->price,
            'ambassador_revenue' => 0.1 * $quantity * $product->price
        ];
    }
}
