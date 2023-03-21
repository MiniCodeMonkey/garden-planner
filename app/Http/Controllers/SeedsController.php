<?php

namespace App\Http\Controllers;

use App\Models\Seed;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class SeedsController extends Controller
{
    public function index(Request $request): Response
    {
        $searchQuery = $request->input('search');

        if ($searchQuery) {
            $seeds = $request->user()->seeds()
                ->where('name', 'LIKE', '%' . $searchQuery . '%')
                ->orWhere('variety', 'LIKE', '%' . $searchQuery . '%')
                ->get();
        } else {
            $seeds = $request->user()->seeds;
        }
        return Inertia::render('Seeds/Index', [
            'seeds' => $seeds,
            'searchQuery' => $searchQuery,
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Seeds/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required'
        ]);

        $seed = new Seed();
        $seed->name = $request->input('name');
        $seed->variety = $request->input('variety');
        $seed->category = $request->input('category');

        $request->user()->seeds()->save($seed);

        return Redirect::route('seeds.show', $seed);
    }

    public function show(Request $request, Seed $seed): Response
    {
        abort_unless($seed->user->id === $request->user()->id, 403);

        return Inertia::render('Seeds/Show', [
            'seed' => $seed,
            'germinations' => $seed->germinations,
        ]);
    }

    public function edit(Request $request, Seed $seed): Response
    {
        // TODO
    }

    public function update(Request $request, Seed $seed): Response
    {
        // TODO
    }

    public function destroy(Request $request, Seed $seed): RedirectResponse
    {
        abort_unless($seed->user->id === $request->user()->id, 403);
        $seed->delete();

        return Redirect::route('dashboard');
    }
}
