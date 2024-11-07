<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Schedule;
use Carbon\Carbon;

class ScheduleSeeder extends Seeder
{
    public function run()
    {
        if (app()->isLocal()) {
            Schedule::factory()
                ->count(31) // 31レコードを生成
                ->sequence(function ($sequence) {
                    // 標準フォーマットの日付を使用
                    $date = Carbon::now()->subDays($sequence->index)->format('Y-m-d');
                    $start_time = '09:00:00';
                    $end_time = '17:00:00';
                    $break_time = '01:00:00';
                    $created_at = Carbon::now()->subDays($sequence->index);
                    $updated_at = Carbon::now()->subDays($sequence->index);

                    return [
                        'date' => $date,
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                        'break_time' => $break_time,
                        'created_at' => $created_at,
                        'updated_at' => $updated_at,
                    ];
                })
                ->create();
        }
    }
}

