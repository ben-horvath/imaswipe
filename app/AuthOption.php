<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthOption extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'provider', 'user_id_at_provider',
    ];

    /**
     * Get the user that owns the auth option.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
