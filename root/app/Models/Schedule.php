<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'start_time', 'end_time', 'break_time'];

    // Accessors
    public function getDateAttribute($value)
    {
        // データベースから取得した日付をY-m-d形式で返す
        return Carbon::parse($value)->format('Y-m-d');
    }

    public function getStartTimeAttribute($value)
    {
        return Carbon::parse($value)->format('H:i');
    }

    public function getEndTimeAttribute($value)
    {
        return Carbon::parse($value)->format('H:i');
    }

    public function getBreakTimeAttribute($value)
    {
        return Carbon::parse($value)->format('H:i');
    }

    // Mutators
    public function setDateAttribute($value)
    {
        try {
            // Y-m-d形式の日付をそのまま保存
            $this->attributes['date'] = Carbon::createFromFormat('Y-m-d', $value)->format('Y-m-d');
        } catch (\Exception $e) {
            throw new \InvalidArgumentException("Invalid date format: " . $value);
        }
    }

    public function setStartTimeAttribute($value)
    {
        try {
            // H:i形式の時間をH:i:s形式に変換して保存
            $this->attributes['start_time'] = Carbon::createFromFormat('H:i', $value)->format('H:i:s');
        } catch (\Exception $e) {
            throw new \InvalidArgumentException("Invalid time format: " . $value);
        }
    }

    public function setEndTimeAttribute($value)
    {
        try {
            // H:i形式の時間をH:i:s形式に変換して保存
            $this->attributes['end_time'] = Carbon::createFromFormat('H:i', $value)->format('H:i:s');
        } catch (\Exception $e) {
            throw new \InvalidArgumentException("Invalid time format: " . $value);
        }
    }

    public function setBreakTimeAttribute($value)
    {
        try {
            // H:i形式の時間をH:i:s形式に変換して保存
            $this->attributes['break_time'] = Carbon::createFromFormat('H:i', $value)->format('H:i:s');
        } catch (\Exception $e) {
            throw new \InvalidArgumentException("Invalid time format: " . $value);
        }
    }
}


