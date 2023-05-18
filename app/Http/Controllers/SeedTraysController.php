<?php

namespace App\Http\Controllers;

use App\Models\SeedTray;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SeedTraysController extends Controller
{
    public function index(Request $request): Response
    {
        $plants = $request->user()->plants;
        return Inertia::render('Trays/Index', [
            'plants' => $plants
        ]);
    }

    public function create(Request $request): Response
    {
        $tray = $request->user()->trays()
            ->where('id', $request->input('tray'))
            ->first();

        abort_unless($tray, 404);

        return Inertia::render('Trays/Create', [
            'tray' => $tray
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tray_id' => ['required', Rule::exists('trays', 'id')->where(function (Builder $query) use ($request) {
                return $query->where('user_id', $request->user()->id);
            })],
            'date' => 'required|date',
            'quantity' => 'required|integer',
            'location' => 'required',
        ]);

        $tray = $request->user()->trays()
            ->where('id', $request->input('tray_id'))
            ->first();

        $plant = new SeedTray();
        $plant->created_at = $request->input('date');
        $plant->quantity = $request->input('quantity');
        $plant->location = $request->input('location');
        $plant->notes = $request->input('notes');

        $tray->plants()->save($plant);

        return Redirect::route('plants.show', $plant);
    }

    public function show(Request $request, SeedTray $plant): Response
    {
        abort_unless($plant->tray->user->id === $request->user()->id, 403);

        return Inertia::render('Trays/Show', [
            'plant' => $plant
        ]);
    }

    public function edit(Request $request, SeedTray $plant): Response
    {
        // TODO
    }

    public function update(Request $request, SeedTray $plant): Response
    {
        // TODO
    }

    public function destroy(Request $request, SeedTray $plant): Response
    {
        // TODO
    }
}
