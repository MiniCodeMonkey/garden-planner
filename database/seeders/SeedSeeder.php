<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Seed;
use App\Models\SeedInventory;

class SeedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = collect(file(__DIR__  . '/inventory.csv'))
            ->map(fn ($row) => str_getcsv($row));

        $headers = $rows->shift();

        $rows->map(fn($row) => array_combine($headers, $row))
            ->each(function ($row) {
                $seed = new Seed();
                $seed->name = $row['Name'];
                $seed->variety = $row['Variety'] === '?' ? null : $row['Variety'];
                $seed->category = $row['Category'];
                $seed->link = $row['Link'];
                $seed->green_house = $row['Green house?'] === 'Yes';

                [$sproutingTimeDaysMin, $sproutingTimeDaysMax] = explode('-', $row['Sprouting time (days)'] . '-');
                $seed->sprouting_time_days_min = $sproutingTimeDaysMin;
                $seed->sprouting_time_days_max = $sproutingTimeDaysMax;
                $seed->sun = $row['Sun'];
                $seed->height = intval($row['Height']);

                [$seedDistanceMin, $seedDistanceMax] = explode('-', $row['Seed distance'] . '-');

                $seed->seed_distance_min = intval($seedDistanceMin);
                $seed->seed_distance_max = $seedDistanceMax ? intval($seedDistanceMax) : $seed->seed_distance_min;

                $seed->seed_depth = floatval($row['Seed depth']);

                $seed->seeding_start = $row['Start seeding'] === '-' ? null : $row['Start seeding'];
                $seed->seeding_end = $row['End seeding'] === '-' ? null : $row['End seeding'];
                $seed->planting_start = $row['Start planting'] === '-' ? null : $row['Start planting'];
                $seed->planting_end = $row['End planting'] === '-' ? null : $row['End planting'];
                $seed->harvest_start = $row['Start harvest'] === '-' ? null : $row['Start harvest'];
                $seed->harvest_end = $row['End Harvest'] === '-' ? null : $row['End Harvest'];
                $seed->inventory_last_checked = $row['Inventory last updated'] === '-' ? null : $row['Inventory last updated'];
                $seed->notes = $row['Notes'] ?? null;

                $seed->save();

                $inventory = new SeedInventory();
                $inventory->quantity = $row['Total seeds'];

                $seed->inventory()->save($inventory);
            });
    }
}
