<?php

namespace App\Http\Controllers;

use App\Models\Sport;
use App\Http\Requests\StoreSportRequest;
use App\Http\Requests\UpdateSportRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class SportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sports = Sport::all();
        return response()->json(['sports' => $sports]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sports',
        ]);

        $sport = Sport::create([
            'name' => $request->name,
        ]);

        return response()->json(['sport' => $sport, 'message' => 'Create a sport successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Sport $sport)
    {
        return response()->json(['sport' => $sport]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sport $sport)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sports,name,' . $sport->id,
        ]);

        $sport->update([
            'name' => $request->name,
        ]);

        return response()->json(['sport' => $sport, 'message' => 'Successful sport update']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sport $sport)
    {
        if ($sport->categories()->count() > 0) {
            return response()->json(['message' => 'This sport cannot be deleted because it has subcategories!'], 422);
        }

        $sport->delete();

        return response()->json(['message' => 'Deleted sport successfully']);
    }
}
