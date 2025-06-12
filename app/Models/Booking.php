<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['property_id', 'date_from', 'date_to'];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
