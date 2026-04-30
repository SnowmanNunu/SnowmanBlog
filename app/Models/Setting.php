<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Setting extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['key', 'value', 'label', 'type'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "设置已{$eventName}: {$this->key}")
            ->useLogName('setting');
    }

    public static function get(string $key, $default = null)
    {
        $cached = Cache::tags(['settings'])->get($key);

        if ($cached !== null) {
            return $cached;
        }

        $value = static::where('key', $key)->value('value');

        if ($value !== null) {
            Cache::tags(['settings'])->put($key, $value, now()->addHour());

            return $value;
        }

        return $default;
    }

    public static function set(string $key, string $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'label' => $key]
        );
        Cache::tags(['settings'])->flush();
    }
}
