<?php

namespace App\Models;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use \App\Models\HistoryTranslations;

class Translations extends Model
{
    protected $table = 'translations';

    protected $fillable = [
        'text_id',
        'text',
        'language_id',
        'translated_at',
    ];

    protected static function booted(){
        static::updating(function (Translations $translation) {
            Log::info('Update text history: ' . $translation->getOriginal("text"));
            HistoryTranslations::create(['text' => $translation->getOriginal('text'),'translation_id' => $translation->getOriginal('id')]);
        });
        static::deleting(function (Translations $translation) {
            Log::info('Delete text history: ' . $translation->getOriginal("text"));
            HistoryTranslations::where("translation_id",$translation->getOriginal('id'))->delete();
        });
    }
}
