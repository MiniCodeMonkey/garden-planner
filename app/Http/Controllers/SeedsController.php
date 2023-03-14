<?php

namespace App\Http\Controllers;

use App\Models\Seed;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SeedsController extends Controller
{
    public function index(Request $request): Response
    {
        $seeds = $request->user()->seeds;
        return Inertia::render('Seeds/Index', [
            'seeds' => $seeds
        ]);
    }

    public function show(Request $request, Seed $seed): Response
    {
        abort_unless($seed->user->id === $request->user()->id, 403);

        return Inertia::render('Seeds/Show', [
            'seed' => $seed
        ]);
    }
}
