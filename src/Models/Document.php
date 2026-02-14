<?php

namespace ME\EmCore\Models;

use ME\EmCore\Models\Folder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;
    protected $table = 'documents';
    protected $guarded   = [];

    protected $fillable = [
        'folder_id', 'name', 'file_path', 'mime_type', 'size', 'user_id'
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }
}
