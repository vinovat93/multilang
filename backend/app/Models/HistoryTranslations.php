<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryTranslations extends Model
{
    protected $table = 'history_translations';

    protected $fillable = [
        'translation_id',
        'text',
    ];
}
