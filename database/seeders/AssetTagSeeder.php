<?php
namespace Innopanda\AssetManager\Database\Seeders;

use Illuminate\Database\Seeder;
use Innopanda\AssetManager\Models\Tag;

class AssetTagSeeder extends Seeder
{
    public function run(): void
    {
        $defaultTags = [
            ['name' => 'Marketing', 'color' => '#ec4899'],   // Pink
            ['name' => 'Exercise', 'color' => '#10b981'],    // Green
            ['name' => 'Membership', 'color' => '#3b82f6'],  // Blue
            ['name' => 'Logo', 'color' => '#f59e0b'],        // Amber
            ['name' => 'Icons', 'color' => '#8b5cf6'],       // Purple
        ];

        foreach ($defaultTags as $tag) {
            Tag::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($tag['name'])],
                $tag
            );
        }
    }
}