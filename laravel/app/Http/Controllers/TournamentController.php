<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index()
    {
        return Tournament::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'max_teams' => 'nullable|integer',
        ]);

        $tournament = Tournament::create($data);

        return response()->json($tournament, 201);
    }

    public function show(Tournament $tournament)
    {
        return $tournament;
    }

    public function update(Request $request, Tournament $tournament)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'location' => 'sometimes|string|max:255',
            'start_date' => 'sometimes|date',
            'max_teams' => 'nullable|integer',
        ]);

        $tournament->update($data);

        return $tournament;
    }

    public function destroy(Tournament $tournament)
    {
        $tournament->delete();

        return response()->json(null, 204);
    }
}
