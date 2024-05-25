<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimeTracker;
use Carbon\Carbon;

class TimeTrackingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $timeTrackers = $user->timeTrackers()->get();
        return response()->json(['time_trackers' => $timeTrackers]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'time_in' => 'required|date',
            'gps_coordinates' => 'required|string',
        ]);

        $user = $request->user();

        // Converting ISO 8601 to a MySQL-friendly format for time_in
        $timeIn = Carbon::parse($validated['time_in'])->format('Y-m-d H:i:s');

        $timeTracker = TimeTracker::create([
            'user_id' => $user->id,
            'time_in' => $timeIn,
            'gps_coordinates' => $validated['gps_coordinates'],
        ]);

        return response()->json(['time_tracker' => $timeTracker], 201);
    }

    public function update(Request $request, $id)
    {
        $timeTracker = TimeTracker::findOrFail($id);

        $validated = $request->validate([
            'time_out' => 'required|date',
            'gps_coordinates' => 'required|string',
        ]);

        // Converting ISO 8601 to a MySQL-friendly format for time_out
        $timeOut = Carbon::parse($validated['time_out'])->format('Y-m-d H:i:s');

        $timeTracker->update([
            'time_out' => $timeOut,
            'gps_coordinates' => $validated['gps_coordinates'],
        ]);

        return response()->json(['time_tracker' => $timeTracker]);
    }

    public function destroy(Request $request, $id)
    {
        $timeTracker = TimeTracker::findOrFail($id);
        $timeTracker->delete();
        return response()->json(['message' => 'Time tracker entry deleted']);
    }
}
