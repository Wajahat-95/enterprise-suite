<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

use function Symfony\Component\Clock\now;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'is_completed',
        'completed_at',
        'tags',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['is_overdue', 'days_until_due'];

    // Relationships
    // Get the user that owns the task.
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getIsOveredueAttribute() {
        if(!$this->due_date || $this->is_completed) {
            return false;
        }
        return $this->due_date->isPast();
    }

    public function getDaysUntilDueAttribute() {
        if(!$this->due_date || $this->is_completed) {
            return null;
        }
        return now()->diffInDays($this->due_date, false);
    }

    // Scopes
    public function scopeCompleted(Builder $query): Builder {
        return $query->where('is_completed', true);
    }

    public function scopePending(Builder $query): Builder {
        return $query->where('is_completed', false);
    }

    public function scopeOverdue(Builder $query): Builder {
        return $query->where('is_completed', false)
        ->whereNotNull('due_date')
        ->whereDate('due_date', '<' , now());

    }
    public function scopePriority(Builder $query, string $priority): Builder {
        return $query->where('priority', $priority);
    }

    public function scopeStatus(Builder $query, string $status): Builder {
        return $query->where('status', $status);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}")
            ->orWhere('description', 'like', "%{$search}");
        });
    }
}
