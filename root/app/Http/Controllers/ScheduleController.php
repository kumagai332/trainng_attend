<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::orderBy('date')->get();
        return view('index', ['schedules' => $schedules]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'break_time' => 'nullable|date_format:H:i',
        ]);

        try {
            $schedule = Schedule::find($request->input('id')); // IDでレコードを検索
            if (!$schedule) {
                $schedule = new Schedule(); // レコードが見つからない場合は新規作成
            }

            $schedule->date = $validatedData['date'];
            $schedule->start_time = $validatedData['start_time'];
            $schedule->end_time = $validatedData['end_time'];
            $schedule->break_time = $validatedData['break_time'];
            $schedule->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}

