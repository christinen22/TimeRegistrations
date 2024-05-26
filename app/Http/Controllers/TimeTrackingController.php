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
            'time_in' => 'required|date_format:Y-m-d\TH:i',
            'gps_coordinates' => 'required|string',
        ]);

        $user = $request->user();
        $timeIn = Carbon::parse($validated['time_in'])->format('Y-m-d H:i:s');

        // Prevent duplicates by checking the last entry
        $lastEntry = $user->timeTrackers()->latest()->first();
        if ($lastEntry && $lastEntry->time_in === $timeIn) {
            return response()->json(['message' => 'Duplicate clock-in prevented'], 409);
        }

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
            'time_in' => 'required|date_format:Y-m-d\TH:i',
            'time_out' => 'nullable|date_format:Y-m-d\TH:i',
            'gps_coordinates' => 'required|string',
        ]);

        $timeIn = Carbon::parse($validated['time_in'])->format('Y-m-d H:i:s');
        $timeOut = $validated['time_out'] ? Carbon::parse($validated['time_out'])->format('Y-m-d H:i:s') : null;

        $timeTracker->update([
            'time_in' => $timeIn,
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
