<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDomain extends Model
{
    protected $primaryKey = 'user_domain_id';
    protected $fillable = [
        'domain', 'name'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'user_id');
    }
}
