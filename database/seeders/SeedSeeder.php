<?php

namespace Database\Seeders;

use App\Models\Seed;
use App\Models\SeedInventory;
use App\Models\User;
use App\WebsiteMetadataDownloader;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SeedSeeder extends Seeder
{
    const IMPORT_DATE_FORMAT = 'm/d/Y';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::findOrFail(1);

        $rows = collect(file(__DIR__ . '/inventory.csv'))
            ->map(fn($row) => str_getcsv($row));

        $headers = $rows->shift();

        $rows->map(fn($row) => array_combine($headers, $row))
            ->each(function ($row) use ($user) {
                echo $row['Name'] . PHP_EOL;

                $seed = new Seed();
                $seed->name = $row['Name'];
                $seed->variety = $row['Variety'] === '?' ? null : $row['Variety'];
                $seed->category = $row['Category'];
                $seed->link = $row['Link'];
                $seed->image_filename = (new WebsiteMetadataDownloader())->downloadImageForUrl($row['Link']);
                $seed->green_house = $row['Green house?'] === 'Yes';

                [$sproutingTimeDaysMin, $sproutingTimeDaysMax] = explode('-', $row['Sprouting time (days)'] . '-');
                $seed->sprouting_time_days_min = $sproutingTimeDaysMin === '?' ? null : $sproutingTimeDaysMin;
                $seed->sprouting_time_days_max = $sproutingTimeDaysMax;
                $seed->sun = $row['Sun'];
                $seed->height = intval($row['Height']);

                [$seedDistanceMin, $seedDistanceMax] = explode('-', $row['Seed distance'] . '-');

                $seed->seed_distance_min = intval($seedDistanceMin);
                $seed->seed_distance_max = $seedDistanceMax ? intval($seedDistanceMax) : $seed->seed_distance_min;

                $seed->seed_depth = floatval($row['Seed depth']);

                $seed->seeding_start = str_contains($row['Start seeding'], '/') ? Carbon::createFromFormat(self::IMPORT_DATE_FORMAT, $row['Start seeding']) : null;
                $seed->seeding_end = str_contains($row['End seeding'], '/') ? Carbon::createFromFormat(self::IMPORT_DATE_FORMAT, $row['End seeding']) : null;
                $seed->planting_start = str_contains($row['Start planting'], '/') ? Carbon::createFromFormat(self::IMPORT_DATE_FORMAT, $row['Start planting']) : null;
                $seed->planting_end = str_contains($row['End planting'], '/') ? Carbon::createFromFormat(self::IMPORT_DATE_FORMAT, $row['End planting']) : null;
                $seed->harvest_start = str_contains($row['Start harvest'], '/') ? Carbon::createFromFormat(self::IMPORT_DATE_FORMAT, $row['Start harvest']) : null;
                $seed->harvest_end = str_contains($row['End Harvest'], '/') ? Carbon::createFromFormat(self::IMPORT_DATE_FORMAT, $row['End Harvest']) : null;
                $seed->inventory_last_checked = str_contains($row['Inventory last updated'], '/') ? Carbon::createFromFormat(self::IMPORT_DATE_FORMAT, $row['Inventory last updated']) : null;
                $seed->notes = $row['Notes'] ?? null;

                $user->seeds()->save($seed);

                $inventory = new SeedInventory();
                $inventory->quantity = $row['Total seeds'];

                $seed->inventory()->save($inventory);
            });
    }
}
