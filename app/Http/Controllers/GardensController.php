<?php

namespace App\Http\Controllers;

use App\Models\Garden;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GardensController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Gardens/Index', [
            'gardens' => $request->user()->gardens,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required',
            'geojson' => 'required',
            'area' => 'required|numeric'
        ]);

        $garden = new Garden();
        $garden->name = $request->input('name');
        $garden->geojson = $request->input('geojson');
        $garden->area = $request->input('area');

        $request->user()->gardens()->save($garden);

        return response()->json($garden);
    }

    public function edit(Request $request, Garden $garden): Response
    {
        // TODO
    }

    public function update(Request $request, Garden $garden): Response
    {
        // TODO
    }

    public function destroy(Request $request, Garden $garden): Response
    {
        // TODO
    }
}
