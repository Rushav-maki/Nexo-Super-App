<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Car;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cars = [
            [
                'name' => 'Alto',
                'type' => 'Hatchback',
                'price_per_day' => 1500,
                'transmission' => 'Manual',
                'seating_capacity' => 4,
                'fuel_type' => 'Petrol',
                'image_url' => 'cars/Alto.jpg',
            ],
            [
                'name' => 'Compass',
                'type' => 'SUV',
                'price_per_day' => 4500,
                'transmission' => 'Automatic',
                'seating_capacity' => 5,
                'fuel_type' => 'Diesel',
                'image_url' => 'cars/Compass.jpg',
            ],
            [
                'name' => 'Creta',
                'type' => 'SUV',
                'price_per_day' => 4200,
                'transmission' => 'Automatic',
                'seating_capacity' => 5,
                'fuel_type' => 'Diesel',
                'image_url' => 'cars/Creta.jpg',
            ],
            [
                'name' => 'Fortuner',
                'type' => 'SUV',
                'price_per_day' => 5500,
                'transmission' => 'Automatic',
                'seating_capacity' => 7,
                'fuel_type' => 'Diesel',
                'image_url' => 'cars/Fortuner.jpg',
            ],
            [
                'name' => 'Jimny',
                'type' => 'Compact SUV',
                'price_per_day' => 3800,
                'transmission' => 'Manual',
                'seating_capacity' => 4,
                'fuel_type' => 'Petrol',
                'image_url' => 'cars/Jimny.jpg',
            ],
            [
                'name' => 'Land Cruiser',
                'type' => 'Luxury SUV',
                'price_per_day' => 9000,
                'transmission' => 'Automatic',
                'seating_capacity' => 7,
                'fuel_type' => 'Diesel',
                'image_url' => 'cars/Land Cruiser.jpg',
            ],
            [
                'name' => 'Leaf',
                'type' => 'Hatchback',
                'price_per_day' => 3000,
                'transmission' => 'Automatic',
                'seating_capacity' => 5,
                'fuel_type' => 'Electric',
                'image_url' => 'cars/Leaf.jpg',
            ],
            [
                'name' => 'Nexon',
                'type' => 'Compact SUV',
                'price_per_day' => 3200,
                'transmission' => 'Manual',
                'seating_capacity' => 5,
                'fuel_type' => 'Diesel',
                'image_url' => 'cars/Nexon.jpg',
            ],
            [
                'name' => 'Rapid',
                'type' => 'Sedan',
                'price_per_day' => 3500,
                'transmission' => 'Automatic',
                'seating_capacity' => 5,
                'fuel_type' => 'Petrol',
                'image_url' => 'cars/Rapid.jpg',
            ],
            [
                'name' => 'Scorpio',
                'type' => 'SUV',
                'price_per_day' => 4800,
                'transmission' => 'Manual',
                'seating_capacity' => 7,
                'fuel_type' => 'Diesel',
                'image_url' => 'cars/Scorpio.jpg',
            ],
            [
                'name' => 'Sportage',
                'type' => 'SUV',
                'price_per_day' => 5000,
                'transmission' => 'Automatic',
                'seating_capacity' => 5,
                'fuel_type' => 'Diesel',
                'image_url' => 'cars/Sportage.jpg',
            ],
            [
                'name' => 'Swift',
                'type' => 'Hatchback',
                'price_per_day' => 1800,
                'transmission' => 'Manual',
                'seating_capacity' => 5,
                'fuel_type' => 'Petrol',
                'image_url' => 'cars/Swift.jpg',
            ],
            [
                'name' => 'Touareg',
                'type' => 'Luxury SUV',
                'price_per_day' => 6500,
                'transmission' => 'Automatic',
                'seating_capacity' => 5,
                'fuel_type' => 'Diesel',
                'image_url' => 'cars/Touareg.jpg',
            ],
        ];

        foreach ($cars as $car) {
            // Use updateOrCreate so seeding is idempotent and won't create duplicates
            Car::updateOrCreate(['name' => $car['name']], $car);
        }
    }
}
