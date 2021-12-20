<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;

class Watched_Movie extends Model
{
    use HasFactory;

    protected $table = 'Watched_Movies';
    // fillableかguardedのどちらかを指定する必要あり
    protected $fillable = [
        'user_id',
        'movie_id',
        'movie_rate'
    ];
    // protected $guarded = [''];

    public static function getWatchedMovie($user_id)
    {
        $movie = Watched_Movie::where('user_id', $user_id)->orderBy('updated_at','desc')->paginate(5);
        return $movie;
    }

    public static function getWatchedMovie_Sortbyrate($user_id)
    {
        $movie = Watched_Movie::where('user_id', $user_id)->orderBy('movie_rate','desc')->orderBy('updated_at','desc')->paginate(5);
        return $movie;
    }

    public static function alreadyWatchedMovie($user_id, $movie_id)
    {
        $id = Watched_Movie::where('user_id', $user_id)->where('movie_id', $movie_id)->count();
        if ($id > 0) {
            return true;
        }
        return false;
    }

    public static function alreadyWatchedMovieRate($user_id, $movie_id)
    {
        $items = Watched_Movie::where('user_id', $user_id)->where('movie_id', $movie_id)->select('movie_rate')->get();
        foreach ($items as $item) {
            $rateitem = $item;
        }
        $rate = $rateitem->original['movie_rate'];
        //dd($rate['original']['movie_rate']);
        return $rate;
    }

    public static function renewWatchedMovieRate($user_id, $movie_id, $movie_rate)
    {
        $items = Watched_Movie::where('user_id' , $user_id)->where('movie_id' , $movie_id)->get();
        foreach ($items as $item) {
            $item->movie_rate = $movie_rate;
            $item->save();
        }
        //dd($rate['original']['movie_rate']);
    }

    public static function addWatchedMovieRate($user_id, $movie_id, $movie_rate)
    {
        Watched_Movie::create(['user_id' => $user_id, 'movie_id' => $movie_id, 'movie_rate' => $movie_rate]);
        //dd($rate['original']['movie_rate']);
    }
}
