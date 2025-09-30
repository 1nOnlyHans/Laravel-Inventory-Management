<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'supplier_name' => fake()->unique()->name(),
            'contact_person' => fake()->unique()->name(),
            'phone' => fake()->unique()->phoneNumber(),
            'email' => fake()->unique()->email(),
            'address' => fake()->unique()->address()
        ];
    }
}
