<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $seedsToSeed = $user->seeds()
            ->where('seeding_start', '<=', now())
            ->where('seeding_end', '>=', now())
            ->doesntHave('plants')
            ->get();

        $seedsToPlant = $user->seeds()
            ->where('planting_start', '<=', now())
            ->where('planting_end', '>=', now())
            ->get();

        $seedsToHarvest = $user->seeds()
            ->where('harvest_start', '<=', now())
            ->where('harvest_end', '>=', now())
            ->get();


        return Inertia::render('Dashboard', [
            'seedsToSeed' => $seedsToSeed,
            'seedsToPlant' => $seedsToPlant,
            'seedsToHarvest' => $seedsToHarvest,
        ]);
    }
}
