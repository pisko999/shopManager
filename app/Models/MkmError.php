<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MkmError extends Model
{
    public const TYPE_NULL = "null";
    public const TYPE_ERROR = "error";

    protected $fillable = ['command', 'method', 'parameters', 'type_answer', 'resolved'];

    public function setParametersAttribute($value)
    {
        $this->attributes['parameters'] = json_encode($value);
    }

    public function getParametersAttribute($value)
    {
        return json_decode($value);
    }

    public function setTypeAnswerAttribute($value)
    {
        if (isset($value->error))
            $this->attributes['type_answer'] = self::TYPE_ERROR;
        else
            $this->attributes['type_answer'] = self::TYPE_NULL;
    }
}
