<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Watched_Movie extends Model
{
    use HasFactory;

    protected $table = 'Watched_Movies';

    public static function getWatchedMovie($user_id) {
        return Watched_Movie::where('user_id',$user_id)->count();
    }
}
