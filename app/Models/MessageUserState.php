<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageUserState extends Model
{
    protected $fillable = ['message_id', 'user_id', 'is_starred', 'is_deleted'];
    protected $casts    = ['is_starred' => 'bool', 'is_deleted' => 'bool'];
}
