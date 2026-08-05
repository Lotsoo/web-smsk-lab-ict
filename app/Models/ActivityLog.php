<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'title',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke user yang melakukan aksi
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope untuk filter berdasarkan entity type
     */
    public function scopeForEntity($query, string $type, ?int $id = null)
    {
        $query->where('entity_type', $type);

        if ($id !== null) {
            $query->where('entity_id', $id);
        }

        return $query;
    }

    /**
     * Scope untuk filter berdasarkan action
     */
    public function scopeWithAction($query, string|array $action)
    {
        if (is_array($action)) {
            return $query->whereIn('action', $action);
        }
        return $query->where('action', $action);
    }

    /**
     * Scope untuk mendapatkan aktivitas terbaru
     */
    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Helper untuk membuat log aktivitas
     */
    public static function log(
        string $action,
        string $entityType,
        string $title,
        ?string $description = null,
        ?int $entityId = null,
        ?array $metadata = null,
        ?int $userId = null
    ): self {
        return self::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'title' => $title,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get human-readable time ago
     */
    public function getTimeAgoAttribute(): string
    {
        $diff = now()->diff($this->created_at);

        if ($diff->y > 0) {
            return $diff->y . ' tahun lalu';
        }
        if ($diff->m > 0) {
            return $diff->m . ' bulan lalu';
        }
        if ($diff->d > 0) {
            return $diff->d . ' hari lalu';
        }
        if ($diff->h > 0) {
            return $diff->h . ' jam lalu';
        }
        if ($diff->i > 0) {
            return $diff->i . ' menit lalu';
        }
        return 'baru saja';
    }

    /**
     * Get icon berdasarkan action
     */
    public function getIconAttribute(): string
    {
        return match ($this->entity_type) {
            'surat_masuk' => 'bi-envelope',
            'surat_keluar' => 'bi-send',
            'surat_revisi' => 'bi-pencil-square',
            default => 'bi-activity',
        };
    }

    /**
     * Get badge color berdasarkan action
     */
    public function getBadgeColorAttribute(): string
    {
        return match ($this->action) {
            'created' => 'bg-primary',
            'updated' => 'bg-warning',
            'deleted' => 'bg-danger',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            'sent' => 'bg-info',
            'revisi_completed' => 'bg-success',
            default => 'bg-secondary',
        };
    }
}
