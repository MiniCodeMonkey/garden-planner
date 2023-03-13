<?php

namespace Database\Seeders;

use Carbon\Carbon;
use DOMDocument;
use Illuminate\Database\Seeder;
use App\Models\Seed;
use App\Models\SeedInventory;
use Illuminate\Support\Facades\Storage;

class SeedSeeder extends Seeder
{
    const IMPORT_DATE_FORMAT = 'd/m/Y';

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
                echo $row['Name'] . PHP_EOL;

                $seed = new Seed();
                $seed->name = $row['Name'];
                $seed->variety = $row['Variety'] === '?' ? null : $row['Variety'];
                $seed->category = $row['Category'];
                $seed->link = $row['Link'];
                $seed->image_filename = $this->downloadImage($row['Link']);
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

                $seed->seeding_start = str_contains($row['Start seeding'], '/') ? Carbon::createFromFormat(self::IMPORT_DATE_FORMAT, $row['Start seeding']) : null;
                $seed->seeding_end = str_contains($row['End seeding'], '/') ? Carbon::createFromFormat(self::IMPORT_DATE_FORMAT, $row['End seeding']) : null;
                $seed->planting_start = str_contains($row['Start planting'], '/') ? Carbon::createFromFormat(self::IMPORT_DATE_FORMAT, $row['Start planting']) : null;
                $seed->planting_end = str_contains($row['End planting'], '/') ? Carbon::createFromFormat(self::IMPORT_DATE_FORMAT, $row['End planting']) : null;
                $seed->harvest_start = str_contains($row['Start harvest'], '/') ? Carbon::createFromFormat(self::IMPORT_DATE_FORMAT, $row['Start harvest']) : null;
                $seed->harvest_end = str_contains($row['End Harvest'], '/') ? Carbon::createFromFormat(self::IMPORT_DATE_FORMAT, $row['End Harvest']) : null;
                $seed->inventory_last_checked = str_contains($row['Inventory last updated'], '/') ? Carbon::createFromFormat(self::IMPORT_DATE_FORMAT, $row['Inventory last updated']) : null;
                $seed->notes = $row['Notes'] ?? null;

                $seed->save();

                $inventory = new SeedInventory();
                $inventory->quantity = $row['Total seeds'];

                $seed->inventory()->save($inventory);
            });
    }

    private function downloadImage(string $url)
    {
        $disk = Storage::disk();

        $options = [
            'http' => [
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36'
            ]
        ];
        $context = stream_context_create($options);

        $url = filter_var($url, FILTER_VALIDATE_URL);
        if (!$url) {
            return null;
        }

        $contents = file_get_contents($url, false, $context);

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($contents);

        foreach ($dom->getElementsByTagName('meta') as $meta) {
            if ($meta->getAttribute('property') === 'og:image') {
                $imageUrl = $meta->getAttribute('content');
                $ext = strtolower(pathinfo($imageUrl, PATHINFO_EXTENSION));
                $ext = explode('?', $ext)[0];

                $filename = 'seeds/' . md5($url) . '.' . $ext;

                if (!$disk->exists($filename)) {
                    $disk->put($filename, file_get_contents($imageUrl, false, $context), 'public');
                }

                return $filename;
            }
        }

        return null;
    }
}
