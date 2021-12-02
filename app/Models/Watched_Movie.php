<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Watched_Movie extends Model
{
    use HasFactory;

    protected $table = 'Watched_Movies';

    public static function getWatchedMovie($user_id) {
        return Watched_Movie::where('user_id',$user_id)->get();
    }

    public static function alreadyWatchedMovie($user_id,$movie_id) {
    $id = Watched_Movie::where('user_id',$user_id)->where('movie_id', $movie_id)->count();
        if($id > 0)
        {
            return true;
        }
        return false;
    }

    public static function alreadyWatchedMovieRate($user_id,$movie_id) {
     $items = Watched_Movie::where('user_id',$user_id)->where('movie_id', $movie_id)->select('movie_rate')->get();
     foreach ($items as $item) 
     {
        $rateitem = $item;
     }
     $rate = $rateitem->original['movie_rate'];
        //dd($rate['original']['movie_rate']);
        return $rate;
    }

}
