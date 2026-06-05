<?php

namespace App\Http\Controllers;

use App\Services\CatalogService;

class HomeController extends Controller
{
    public function __construct(private readonly CatalogService $catalog) {}

    public function index()
    {
        $newArrivals = $this->catalog->newArrivals(8);
        $popular     = $this->catalog->popular(8);

        return view('home', compact('newArrivals', 'popular'));
    }
}
