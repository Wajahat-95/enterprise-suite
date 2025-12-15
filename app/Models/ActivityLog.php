<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Get the user that performed the action
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Get the related model (polymorphic).
    public function model () {
        return $this->morphTo();
    }

    // Scope to filter by action type
    public function scopeAction($query, $action) {
        return $query->where('action', $action);
    }

    // Scope to filter by model type
    public function scopeModelType($query, $modelType) {
        return $query->where('model_type', $modelType);
    }

    // Scope to get recent activities
    public function scopeRecent($query, $limit = 10) {
        return $query->latest()->limit($limit);
    }

    // Helper method to log activities
    public static function log(string $action, string $description, ?Model $model = null, array $properties = []) {
        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
