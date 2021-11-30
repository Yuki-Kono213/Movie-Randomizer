<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WatchedMovieTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'user_id' => 1,
            'movie_id' => 50,
            'movie_rate' => 5
 
        ];
        DB::table('watched_movies')->insert($param);
        
        $param = [
            'user_id' => 2,
            'movie_id' => 55,
            'movie_rate' => 4
 
        ];
        DB::table('watched_movies')->insert($param);
        
        $param = [
            'user_id' => 1,
            'movie_id' => 1200,
            'movie_rate' => 5
 
        ];
        DB::table('watched_movies')->insert($param);
       
        $param = [
            'user_id' => 1,
            'movie_id' => 1450,
            'movie_rate' => 3
 
        ];
        DB::table('watched_movies')->insert($param);
        
        $param = [
            'user_id' => 2,
            'movie_id' => 53,
            'movie_rate' => 2
 
        ];
        DB::table('watched_movies')->insert($param);
       
        $param = [
            'user_id' => 3,
            'movie_id' => 2150,
            'movie_rate' => 5
 
        ];
        DB::table('watched_movies')->insert($param);
        
        $param = [
            'user_id' => 3,
            'movie_id' => 3450,
            'movie_rate' => 3
 
        ];
        DB::table('watched_movies')->insert($param);
        
        $param = [
            'user_id' => 2,
            'movie_id' => 135,
            'movie_rate' => 4
 
        ];
        DB::table('watched_movies')->insert($param);
        
        $param = [
            'user_id' => 3,
            'movie_id' => 140,
            'movie_rate' => 4
 
        ];
        DB::table('watched_movies')->insert($param);
        
        $param = [
            'user_id' => 4,
            'movie_id' => 125,
            'movie_rate' => 3
 
        ];
        DB::table('watched_movies')->insert($param);
        
        $param = [
            'user_id' => 5,
            'movie_id' => 5678,
            'movie_rate' => 1
 
        ];
        DB::table('watched_movies')->insert($param);
        
        $param = [
            'user_id' => 6,
            'movie_id' => 11111,
            'movie_rate' => 3
 
        ];
        DB::table('watched_movies')->insert($param);
        
        $param = [
            'user_id' =>6,
            'movie_id' => 22222,
            'movie_rate' =>4
 
        ];
        DB::table('watched_movies')->insert($param);
    }
}
