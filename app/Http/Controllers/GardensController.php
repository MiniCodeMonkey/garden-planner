<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GardensController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Gardens/Index', [
            'gardens' => [],
        ]);
    }
}
