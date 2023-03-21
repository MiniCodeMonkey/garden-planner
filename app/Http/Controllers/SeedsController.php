<?php

namespace App\Http\Controllers;

use App\Models\Seed;
use App\Models\User;
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

        $currentFilters = [
            'search' => $searchQuery,
            'sort' => $request->input('sort', 'newest'),
            'category' => $request->input('category', []),
            'sun' => $request->input('sun', []),
            'greenhouse' => $request->input('greenhouse'),
        ];

        $seeds = $request->user()->seeds();

        if ($searchQuery) {
            $seeds->where(function ($query) use ($searchQuery) {
                $query->where('name', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('variety', 'LIKE', '%' . $searchQuery . '%');
            });
        }

        if (count($currentFilters['category']) > 0) {
            $seeds->where(function ($query) use ($currentFilters, $searchQuery) {
                foreach ($currentFilters['category'] as $category) {
                    $query->orWhere('category', $category);
                }
            });
        }

        if (count($currentFilters['sun']) > 0) {
            $seeds->where(function ($query) use ($currentFilters, $searchQuery) {
                foreach ($currentFilters['sun'] as $sun) {
                    $query->orWhere('sun', $sun);
                }
            });
        }

        if ($currentFilters['greenhouse'] !== null) {
            $seeds->where('green_house', $currentFilters['greenhouse'] === 'yes');
        }

        if ($currentFilters['sort'] === 'newest') {
            $seeds->orderBy('created_at', 'desc');
        } elseif ($currentFilters['sort'] === 'oldest') {
            $seeds->orderBy('created_at', 'asc');
        } elseif ($currentFilters['sort'] === 'most') {
            $seeds->withSum('inventory', 'quantity')->orderBy('inventory_sum_quantity', 'desc');
        } elseif ($currentFilters['sort'] === 'least') {
            $seeds->withSum('inventory', 'quantity')->orderBy('inventory_sum_quantity', 'asc');
        }

        return Inertia::render('Seeds/Index', [
            'seeds' => $seeds->get(),
            'filterOptions' => [
                'category' => $this->getDistinctValues($request->user(), 'category'),
                'sun' => $this->getDistinctValues($request->user(), 'sun'),
            ],
            'currentFilters' => $currentFilters
        ]);
    }

    private function getDistinctValues(User $user, string $column)
    {
        return $user->seeds()
            ->select($column)
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->groupBy($column)
            ->pluck($column)
            ->map(function ($item) {
                return [
                    'label' => $item,
                    'value' => $item,
                ];
            });
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
        abort_unless($seed->user->id === $request->user()->id, 403);

        return Inertia::render('Seeds/Edit', [
            'seed' => $seed
        ]);
    }

    public function update(Request $request, Seed $seed): RedirectResponse
    {
        abort_unless($seed->user->id === $request->user()->id, 403);

        $request->validate([
            'name' => 'required',
            'category' => 'required'
        ]);

        $seed->name = $request->input('name');
        $seed->variety = $request->input('variety');
        $seed->category = $request->input('category');
        $seed->link = $request->input('link');
        $seed->green_house = $request->input('green_house');
        $seed->sprouting_time_days_min = $request->input('sprouting_time_days_min');
        $seed->sprouting_time_days_max = $request->input('sprouting_time_days_max');
        $seed->sun = $request->input('sun');
        $seed->height = $request->input('height');
        $seed->seed_distance_min = $request->input('seed_distance_min');
        $seed->seed_distance_max = $request->input('seed_distance_max');
        $seed->seed_depth = $request->input('seed_depth');
        $seed->seeding_start = $request->input('seeding_start');
        $seed->seeding_end = $request->input('seeding_end');
        $seed->planting_start = $request->input('planting_start');
        $seed->planting_end = $request->input('planting_end');
        $seed->harvest_start = $request->input('harvest_start');
        $seed->harvest_end = $request->input('harvest_end');
        $seed->inventory_last_checked = $request->input('inventory_last_checked');
        $seed->notes = $request->input('notes');
        $seed->save();

        return Redirect::route('seeds.show', $seed);
    }

    public function destroy(Request $request, Seed $seed): RedirectResponse
    {
        abort_unless($seed->user->id === $request->user()->id, 403);
        $seed->delete();

        return Redirect::route('dashboard');
    }
}
