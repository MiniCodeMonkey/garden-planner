<?php

namespace App\Http\Controllers;

use App\Models\Seed;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class CalendarController extends Controller
{
    public function __invoke()
    {
        $seeds = Seed::all();
        $eventDates = $this->expandDates($seeds);
        $dates = $this->buildDates($eventDates, date('Y'), date('m'));

        return view('calendar', ['seeds' => $seeds, 'dates' => $dates]);
    }

    private function expandDates(Collection $seeds)
    {
        $dates = [];

        $keys = [
            'seeding_start' => 'Seed',
            'seeding_end' => 'Done seeding',
            'planting_start' => 'Plant',
            'planting_end' => 'Done planting',
            'harvest_start' => 'Harvest',
            'harvest_end' => 'Done harvesting',
        ];

        foreach ($seeds as $seed) {
            foreach ($keys as $key => $description) {
                if ($seed->$key) {
                    $date = $seed->$key->format('Y-m-d');

                    if (!isset($dates[$date])) {
                        $dates[$date] = [];
                    }

                    $dates[$date][] = [
                      'name' => $description . ' ' . $seed->name,
                      'url' => url('seeds/' . $seed->id)
                    ];
                }
            }
        }

        return $dates;
    }

    private function buildDates(array $eventDates, int $year, int $month)
    {
        $dates = [];
        $parsedYearMonth = Carbon::parse($year . '-' . $month . '-01');

        $date = $parsedYearMonth->copy()->startOfMonth()->startOfWeek();
        $endDate = $parsedYearMonth->copy()->endOfMonth()->endOfWeek();

        while ($date->lessThan($endDate)) {
            $formattedDate = $date->format('Y-m-d');
            $dates[] = [
                'date' => $formattedDate,
                'isCurrentMonth' => $date->isCurrentMonth(),
                'events' => $eventDates[$formattedDate] ?? [],
            ];
            $date->addDay();
        }

        return $dates;
    }
}
