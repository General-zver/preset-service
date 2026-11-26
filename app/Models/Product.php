<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Product extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'price',
        'composition',
        'preset_id',
    ];

    public function preset() : BelongsTo {
        return $this->belongsTo(Preset::class);
    }
}
