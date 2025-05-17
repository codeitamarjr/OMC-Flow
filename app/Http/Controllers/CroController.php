<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Core\CroSearchService;

class CroController extends Controller
{
    public function search(Request $request, CroSearchService $cro)
    {
        $term = $request->input('term');

        if (!$term) {
            return response()->json(['error' => 'Search term is required.'], 422);
        }

        try {
            $results = $cro->search($term);
            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
