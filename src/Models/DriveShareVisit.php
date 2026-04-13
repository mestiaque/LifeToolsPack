<?php

namespace ME\EmCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriveShareVisit extends Model
{
    protected $fillable = [
        'share_type',
        'document_id',
        'folder_id',
        'share_token',
        'ip_address',
        'visited_url',
        'referer',
        'user_agent',
        'browser',
        'os',
        'device_type',
        'device_name',
        'visit_count',
        'first_visited_at',
        'last_visited_at',
    ];

    protected $casts = [
        'first_visited_at' => 'datetime',
        'last_visited_at' => 'datetime',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }
}
