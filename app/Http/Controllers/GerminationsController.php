<?php

namespace App\Http\Controllers;

use App\Models\Germination;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class GerminationsController extends Controller
{
    public function index(Request $request): Response
    {
        $germinations = $request->user()->germinations;
        return Inertia::render('Germinations/Index', [
            'germinations' => $germinations
        ]);
    }

    public function create(Request $request): Response
    {
        $seed = $request->user()->seeds()
            ->where('id', $request->input('seed'))
            ->first();

        abort_unless($seed, 404);

        return Inertia::render('Germinations/Create', [
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

        $germination = new Germination();
        $germination->created_at = $request->input('date');
        $germination->quantity = $request->input('quantity');
        $germination->location = $request->input('location');
        $germination->notes = $request->input('notes');

        $seed->germinations()->save($germination);

        return Redirect::route('germinations.show', $germination);
    }

    public function show(Request $request, Germination $germination): Response
    {
        abort_unless($germination->seed->user->id === $request->user()->id, 403);

        return Inertia::render('Germinations/Show', [
            'germination' => $germination
        ]);
    }

    public function edit(Request $request, Germination $germination): Response
    {
        // TODO
    }

    public function update(Request $request, Germination $germination): Response
    {
        // TODO
    }

    public function destroy(Request $request, Germination $germination): Response
    {
        // TODO
    }
}
