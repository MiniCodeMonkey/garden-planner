<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PlantsController extends Controller
{
    public function index(Request $request): Response
    {
        $plants = $request->user()->plants;
        return Inertia::render('Plants/Index', [
            'plants' => $plants
        ]);
    }

    public function create(Request $request): Response
    {
        $seed = $request->user()->seeds()
            ->where('id', $request->input('seed'))
            ->first();

        abort_unless($seed, 404);

        return Inertia::render('Plants/Create', [
            'seed' => $seed
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'seed_id' => ['required', Rule::exists('seeds', 'id')->where(function (Builder $query) use ($request) {
                return $query->where('user_id', $request->user()->id);
            })],
            'date' => 'required|date',
            'quantity' => 'required|integer',
            'location' => 'required',
        ]);

        $seed = $request->user()->seeds()
            ->where('id', $request->input('seed_id'))
            ->first();

        $plant = new Plant();
        $plant->created_at = $request->input('date');
        $plant->quantity = $request->input('quantity');
        $plant->location = $request->input('location');
        $plant->notes = $request->input('notes');

        $seed->plants()->save($plant);

        return Redirect::route('plants.show', $plant);
    }

    public function show(Request $request, Plant $plant): Response
    {
        abort_unless($plant->seed->user->id === $request->user()->id, 403);

        return Inertia::render('Plants/Show', [
            'plant' => $plant
        ]);
    }

    public function edit(Request $request, Plant $plant): Response
    {
        // TODO
    }

    public function update(Request $request, Plant $plant): Response
    {
        // TODO
    }

    public function destroy(Request $request, Plant $plant): Response
    {
        // TODO
    }
}
