<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReminderHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'fk_user_id', 'number_of_medicines'
    ];
}
