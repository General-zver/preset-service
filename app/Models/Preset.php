<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Preset extends Model
{
    use HasFactory, Notifiable;
    
    protected $fillable = [
        'name',
        'status',
        'preset_price',
    ];


    public function getNormalizedStstus() : String {
        return $this->status ? "Забронирован" : "Доступен";
    }

    public function getRelatedProducts() {
        return $this->hasMany(Product::class);
    }
}
