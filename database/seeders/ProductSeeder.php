<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Ordinateur portable',
                'description' => 'Ordinateur portable performant avec processeur Intel i7 et 16GB de RAM',
                'quantite' => 25,
            ],
            [
                'name' => 'Souris sans fil',
                'description' => 'Souris optique sans fil avec une autonomie de 6 mois',
                'quantite' => 50,
            ],
            [
                'name' => 'Clavier mécanique',
                'description' => 'Clavier mécanique avec switches Cherry MX Blue et rétroéclairage RGB',
                'quantite' => 30,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
