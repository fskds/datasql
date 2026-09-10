<?php

namespace App\Models\Attachment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Temp extends Model
{
    use HasFactory;

    protected $table = 'attachment_temps';

    protected $fillable = [
        'name',
        'path',
        'size',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }
}
