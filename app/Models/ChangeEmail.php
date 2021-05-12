<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChangeEmail extends Model
{
    protected $fillable = ['old_email_address', 'email_address', 'hash', 'fulfilled'];

    public function User()
    {
        return $this->belongsTo(User::class);
    }
}
