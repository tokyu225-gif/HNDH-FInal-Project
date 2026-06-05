<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BeachSeeder extends Seeder
{
    public function run(): void
    {
        $beaches = [
            [
                'name' => 'Tanjung Aan Beach',
                'description' => 'One of East Lombok\'s most iconic beaches, featuring distinctive double crescent bays with powdery white sand and crystal-clear turquoise waters. Perfect for swimming, snorkeling, and sunbathing with stunning hilltop viewpoints.',
                'image_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&fit=crop',
                'latitude' => -8.8833,
                'longitude' => 116.3833,
            ],
            [
                'name' => 'Pink Beach (Pantai Tangsi)',
                'description' => 'A unique beach with naturally pink-hued sand caused by red coral fragments mixing with white sand. Located on the southeastern coast near Labuhan Lombok, offering excellent snorkeling with vibrant marine life and coral reefs.',
                'image_url' => 'https://images.unsplash.com/photo-1519046904884-53103b34b206?w=800&fit=crop',
                'latitude' => -8.4167,
                'longitude' => 116.7167,
            ],
            [
                'name' => 'Kuta Beach Lombok',
                'description' => 'A long stretch of white sandy beach with powerful waves, popular with surfers. An unspoiled alternative to Bali\'s Kuta, featuring a laid-back atmosphere and dramatic coastal landscapes with crystal waters.',
                'image_url' => 'https://images.unsplash.com/photo-1590523741831-ab7ac8b14f04?w=800&fit=crop',
                'latitude' => -8.9000,
                'longitude' => 116.2833,
            ],
            [
                'name' => 'Labuhan Lombok Beach',
                'description' => 'The main port beach and gateway to Sumbawa, with views of traditional fishing boats and the bustling harbor. A working beach offering an authentic local experience and fresh seafood.',
                'image_url' => 'https://images.unsplash.com/photo-1495954484750-af469f2f9be5?w=800&fit=crop',
                'latitude' => -8.5000,
                'longitude' => 116.6667,
            ],
            [
                'name' => 'Mawun Beach',
                'description' => 'A small, horseshoe-shaped beach with soft white sand and calm turquoise waters protected by surrounding green cliffs. Excellent for families and swimming, less crowded than nearby beaches.',
                'image_url' => 'https://images.unsplash.com/photo-1520454974749-d2de9c4ee218?w=800&fit=crop',
                'latitude' => -8.9500,
                'longitude' => 116.3500,
            ],
            [
                'name' => 'Gili Sulat',
                'description' => 'A small protected island off the northeast coast of East Lombok offering pristine, undeveloped beaches with excellent snorkeling. Part of a marine conservation area with rich biodiversity.',
                'image_url' => 'https://images.unsplash.com/photo-1537956965359-7573183d1f57?w=800&fit=crop',
                'latitude' => -8.3333,
                'longitude' => 116.7000,
            ],
            [
                'name' => 'Gili Lawang',
                'description' => 'Another gem island off East Lombok\'s northeast coast, known for pristine corals and vibrant marine life. Less touristy than the western Gili Islands, offering a peaceful island escape.',
                'image_url' => 'https://images.unsplash.com/photo-1502086223501-7ea6ecd79368?w=800&fit=crop',
                'latitude' => -8.3167,
                'longitude' => 116.7167,
            ],
            [
                'name' => 'Segenter Beach',
                'description' => 'A hidden gem with stunning turquoise waters and white sand on the southern coast of East Lombok. Known for its quiet, undeveloped character—perfect for photography and peaceful relaxation.',
                'image_url' => 'https://images.unsplash.com/photo-1540202404-a2f29016b523?w=800&fit=crop',
                'latitude' => -8.9167,
                'longitude' => 116.4167,
            ],
            [
                'name' => 'Tanjung Luar Beach',
                'description' => 'East Lombok\'s easternmost coastal point with dramatic sea cliffs, rugged scenery, and strong ocean winds. Popular with experienced swimmers, photographers, and those seeking untamed natural beauty.',
                'image_url' => 'https://images.unsplash.com/photo-1566024144545-9c2e25fb3cd8?w=800&fit=crop',
                'latitude' => -8.7833,
                'longitude' => 116.5833,
            ],
            [
                'name' => 'Gili Bidara (Pasaran Island)',
                'description' => 'A small island off the east coast featuring pristine white sand beaches and excellent snorkeling opportunities. Known for marine biodiversity and glimpses of traditional Sasak island life.',
                'image_url' => 'https://images.unsplash.com/photo-1559128010-7c1ad6e1b6a5?w=800&fit=crop',
                'latitude' => -8.3500,
                'longitude' => 116.6833,
            ],
            [
                'name' => 'Pandanan Bay',
                'description' => 'A scenic bay area with calm waters, white sand beaches, and surrounding green hills creating a picture-perfect setting. Ideal for swimming, picnicking, and enjoying stunning sunset views.',
                'image_url' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&fit=crop',
                'latitude' => -8.8500,
                'longitude' => 116.4500,
            ],
            [
                'name' => 'Selong Belanak Beach',
                'description' => 'A sweeping bay with gentle waves and soft white sand, perfect for beginner surfers and swimming. Backed by rolling green hills and traditional fishing villages, offering an authentic Lombok experience.',
                'image_url' => 'https://images.unsplash.com/photo-1537956965359-7573183d1f57?w=800&fit=crop',
                'latitude' => -8.8833,
                'longitude' => 116.1667,
            ],
        ];

        foreach ($beaches as $beach) {
            DB::table('beaches')->insert(array_merge($beach, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }
    }
}
