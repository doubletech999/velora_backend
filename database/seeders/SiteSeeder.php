<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Site;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sites = [
            [
                'name' => 'Church of the Nativity',
                'description' => 'The Church of the Nativity in Bethlehem is one of the oldest continuously operating Christian churches in the world. Built over the cave where Jesus Christ is believed to have been born.',
                'latitude' => 31.7040,
                'longitude' => 35.2066,
                'type' => 'historical',
                'image_url' => 'https://example.com/church-nativity.jpg'
            ],
            [
                'name' => 'Al-Aqsa Mosque',
                'description' => 'Al-Aqsa Mosque is the third holiest site in Islam, located in the Old City of Jerusalem. It is part of the larger Al-Aqsa compound.',
                'latitude' => 31.7761,
                'longitude' => 35.2358,
                'type' => 'historical',
                'image_url' => 'https://example.com/al-aqsa.jpg'
            ],
            [
                'name' => 'Dead Sea',
                'description' => 'The Dead Sea is a salt lake bordered by Jordan to the east and Israel and Palestine to the west. It is famous for its high salt content and therapeutic properties.',
                'latitude' => 31.5590,
                'longitude' => 35.4732,
                'type' => 'natural',
                'image_url' => 'https://example.com/dead-sea.jpg'
            ],
            [
                'name' => 'Hisham\'s Palace',
                'description' => 'Hisham\'s Palace is an important early Islamic archaeological site located in Jericho. It was built during the Umayyad period.',
                'latitude' => 31.8667,
                'longitude' => 35.4333,
                'type' => 'historical',
                'image_url' => 'https://example.com/hishams-palace.jpg'
            ],
            [
                'name' => 'Ramallah Cultural Palace',
                'description' => 'A modern cultural center in Ramallah that hosts various cultural events, exhibitions, and performances showcasing Palestinian arts and culture.',
                'latitude' => 31.9038,
                'longitude' => 35.2034,
                'type' => 'cultural',
                'image_url' => 'https://example.com/ramallah-cultural.jpg'
            ],
            [
                'name' => 'Hebron Old City',
                'description' => 'The Old City of Hebron is home to the Cave of the Patriarchs and represents one of the oldest continuously inhabited cities in the world.',
                'latitude' => 31.5326,
                'longitude' => 35.0998,
                'type' => 'historical',
                'image_url' => 'https://example.com/hebron-old-city.jpg'
            ]
        ];

        foreach ($sites as $site) {
            Site::create($site);
        }
    }
}
