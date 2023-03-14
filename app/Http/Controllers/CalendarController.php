<?php

namespace App\Http\Controllers;

use App\Models\Seed;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CalendarController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function index(Request $request): Response
    {
        $seeds = Seed::all();
        $eventDates = $this->expandDates($seeds);
        $days = $this->buildDays($eventDates, date('Y'), date('m'));

        return Inertia::render('Calendar/Index', [
            'seeds' => $seeds,
            'days' => $days,
        ]);
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

    private function buildDays(array $eventDates, int $year, int $month)
    {
        $days = [];
        $parsedYearMonth = Carbon::parse($year . '-' . $month . '-01');

        $date = $parsedYearMonth->copy()->startOfMonth()->startOfWeek();
        $endDate = $parsedYearMonth->copy()->endOfMonth()->endOfWeek();

        while ($date->lessThan($endDate)) {
            $formattedDate = $date->format('Y-m-d');
            $days[] = [
                'date' => $formattedDate,
                'isToday' => $date->isToday(),
                'isCurrentMonth' => $date->isCurrentMonth(),
                'events' => $eventDates[$formattedDate] ?? [],
            ];
            $date->addDay();
        }

        return $days;
    }
}
