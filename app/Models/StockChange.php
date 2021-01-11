<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockChange extends Model
{
    protected $fillable = ['type','stock_id', 'id_article_MKM', 'data1', 'data2', 'batch'];

    public $timestamps = false;

    public function Stock(){
        return $this->belongsTo("\App\Models\Stock");
    }
}
