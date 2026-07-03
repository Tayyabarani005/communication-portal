<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class File extends Model
{
    use HasFactory;

    protected $table = 'file';
    protected $primaryKey = 'file_id';

    protected $fillable = [
        'attachable_id',
        'attachable_type',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}
