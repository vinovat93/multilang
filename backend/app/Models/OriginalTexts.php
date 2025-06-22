<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OriginalTexts extends Model
{
    protected $table = 'texts';

    protected $fillable = [
        'text',
        'language_id',
    ];
}
