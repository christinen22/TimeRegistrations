<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimeTracking;

class TimeTrackingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $timeTrackings = $user->timeTrackings()->get();
        return response()->json(['time_trackings' => $timeTrackings]);
    }

    public function store(Request $request)
    {
        $request->validate([
            // Validation rules for creating a new time tracking entry
        ]);

        $timeTracking = TimeTracking::create([
            'user_id' => $request->user()->id,
            // Fill in other fields as needed
        ]);

        return response()->json(['time_tracking' => $timeTracking], 201);
    }

    public function update(Request $request, $id)
    {
        $timeTracking = TimeTracking::findOrFail($id);

        $request->validate([
            // Validation rules for updating a time tracking entry
        ]);

        $timeTracking->update([
            // Fill in fields to update
        ]);

        return response()->json(['message' => 'Time tracking entry updated']);
    }

    public function destroy(Request $request, $id)
    {
        $timeTracking = TimeTracking::findOrFail($id);
        $timeTracking->delete();
        return response()->json(['message' => 'Time tracking entry deleted']);
    }
}
