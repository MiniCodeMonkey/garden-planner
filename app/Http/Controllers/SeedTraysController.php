<?php

namespace App\Http\Controllers;

use App\Models\SeedTray;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class SeedTraysController extends Controller
{
    public function index(Request $request): Response
    {
        $trays = $request->user()->trays;

        return Inertia::render('Trays/Index', [
            'trays' => $trays
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Trays/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'rows' => 'required|integer',
            'columns' => 'required|integer',
        ]);

        $tray = new SeedTray();
        $tray->name = $request->input('name');
        $tray->type = $request->input('type');
        $tray->rows = $request->input('rows');
        $tray->columns = $request->input('columns');

        $request->user()->trays()->save($tray);

        return Redirect::route('trays.show', $tray);
    }

    public function show(Request $request, SeedTray $tray): Response
    {
        abort_unless($tray->user->id === $request->user()->id, 403);
        $plants = $tray->plants()
            ->with('seed')
            ->get()
            ->mapWithKeys(function ($plant) {
                $locations = collect(explode('|', $plant->seed_tray_locations));

                return $locations->mapWithKeys(function ($location) use ($plant) {
                    return [$location => $plant];
                });
            });

        return Inertia::render('Trays/Show', [
            'tray' => $tray,
            'plants' => $plants,
        ]);
    }

    public function edit(Request $request, SeedTray $tray): Response
    {
        // TODO
    }

    public function update(Request $request, SeedTray $tray): Response
    {
        // TODO
    }

    public function destroy(Request $request, SeedTray $tray): Response
    {
        // TODO
    }
}
