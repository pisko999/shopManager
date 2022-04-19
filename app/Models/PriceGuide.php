<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class PriceGuide extends Model
{

    public $fillable = [
        'idProduct',
        'date',
        'average',
        'lov',
        'trend',
        'germanProLow',
        'suggested',
        'foilSell',
        'foilLow',
        'foilTrend',
        'lowEx',
        'avgOne',
        'avgSeven',
        'avgThirty',
        'foilAvgOne',
        'foilAvgSeven',
        'foilAvgThirty'
        ];

    public $timestamps = false;
    public $incrementing = false;

    public function product()
    {
        return $this->belongsTo('\App\Models\AllProduct', 'id', 'idProduct');
    }
}
