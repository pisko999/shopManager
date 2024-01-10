<?php

namespace App\Http\Controllers;

use App\Repositories\AllProductsRepositoryInterface;
use App\Repositories\StockRepositoryInterface;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private AllProductsRepositoryInterface $productsRepository;
    public function __construct(AllProductsRepositoryInterface $productsRepository)
    {
        $this->productsRepository = $productsRepository;
    }

    public function search(Request $request) {
//        \Debugbar::info($this->productsRepository->search($request));
//        return view('home');

        try {
            $results = $this->productsRepository->search($request);
            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
