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
