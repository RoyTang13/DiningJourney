<?php

namespace Database\Seeders;

use App\Models\Place;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $places = [
            [
                'type' => 'attraction',
                'name' => 'Petronas Twin Towers',
                'category' => 'Culture & Heritage',
                'area' => 'Kuala Lumpur City Centre',
                'address' => 'Petronas Twin Towers, Kuala Lumpur City Centre, 50088 Kuala Lumpur',
                'description' => 'Visit the iconic twin skyscrapers, explore the skybridge, and enjoy panoramic views of Kuala Lumpur.',
                'image_path' => '/images/kuala-lumpur-skyline.jpg',
                'rating' => 4.8,
                'review_count' => 12000,
                'price_level' => 2,
                'tags' => ['architecture', 'culture', 'city views', 'family'],
                'latitude' => 3.1579,
                'longitude' => 101.7116,
            ],
            [
                'type' => 'attraction',
                'name' => 'Batu Caves',
                'category' => 'Nature & Parks',
                'area' => 'Gombak, Selangor',
                'address' => 'Batu Caves, 68100 Batu Caves, Selangor',
                'description' => 'Climb the colourful staircase to a limestone cave complex and one of the region’s best-known Hindu shrines.',
                'image_path' => 'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?auto=format&fit=crop&w=900&q=85',
                'rating' => 4.7,
                'review_count' => 8000,
                'price_level' => 1,
                'tags' => ['culture', 'temple', 'outdoor', 'photography'],
                'latitude' => 3.2379,
                'longitude' => 101.6840,
            ],
            [
                'type' => 'attraction',
                'name' => 'Kuala Lumpur Tower',
                'category' => 'Entertainment',
                'area' => 'Kuala Lumpur',
                'address' => '2 Jalan Punchak, Off Jalan P. Ramlee, 50250 Kuala Lumpur',
                'description' => 'Take in sweeping city views from the observation deck of one of Kuala Lumpur’s signature landmarks.',
                'image_path' => 'https://images.unsplash.com/photo-1516306580123-e6e52b1b7b5f?auto=format&fit=crop&w=900&q=85',
                'rating' => 4.6,
                'review_count' => 6000,
                'price_level' => 2,
                'tags' => ['architecture', 'city views', 'family', 'photography'],
                'latitude' => 3.1528,
                'longitude' => 101.7038,
            ],
            [
                'type' => 'restaurant',
                'name' => 'Jalan Alor Food Street',
                'category' => 'Street Food',
                'area' => 'Bukit Bintang',
                'address' => 'Jalan Alor, Bukit Bintang, 50200 Kuala Lumpur',
                'description' => 'Explore a lively night-time food street filled with Malaysian favourites, grilled seafood, and local snacks.',
                'image_path' => 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=900&q=85',
                'rating' => 4.7,
                'review_count' => 10000,
                'price_level' => 2,
                'tags' => ['street food', 'malaysian', 'night market', 'halal'],
                'latitude' => 3.1457,
                'longitude' => 101.7050,
            ],
            [
                'type' => 'restaurant',
                'name' => 'Nasi Lemak Wanjo',
                'category' => 'Malaysian',
                'area' => 'Kampung Baru',
                'address' => '8 Jalan Raja Muda Musa, Kampung Baru, 50300 Kuala Lumpur',
                'description' => 'Try Malaysia’s much-loved coconut rice with sambal, crunchy anchovies, peanuts, egg, and your choice of sides.',
                'image_path' => 'https://images.unsplash.com/photo-1563379926898-05f4575a45d8?auto=format&fit=crop&w=900&q=85',
                'rating' => 4.8,
                'review_count' => 7000,
                'price_level' => 1,
                'tags' => ['malaysian', 'local food', 'halal', 'budget'],
                'latitude' => 3.1659,
                'longitude' => 101.7094,
            ],
            [
                'type' => 'restaurant',
                'name' => 'Restoran Rebung',
                'category' => 'Malaysian',
                'area' => 'Bangsar',
                'address' => '5-2, Level 5, Menara Felda, Persiaran KLCC, 50450 Kuala Lumpur',
                'description' => 'Enjoy a broad selection of Malaysian home-style dishes in a relaxed buffet setting.',
                'image_path' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&w=900&q=85',
                'rating' => 4.6,
                'review_count' => 5000,
                'price_level' => 3,
                'tags' => ['malaysian', 'buffet', 'family', 'halal'],
                'latitude' => 3.1689,
                'longitude' => 101.7152,
            ],
        ];

        foreach ($places as $place) {
            $slug = Str::slug($place['name']);
            Place::updateOrCreate(['slug' => $slug], $place + ['slug' => $slug]);
        }
    }
}
