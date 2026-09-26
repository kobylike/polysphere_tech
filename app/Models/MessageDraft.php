<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageDraft extends Model
{
    protected $fillable = ['user_id', 'friend_id', 'body'];
}
