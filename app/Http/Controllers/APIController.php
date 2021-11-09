<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class APIController extends Controller
{
    function index()
    {
        $apikey = "e9678255150ea732f1e1c718fd75ed6d"; //TMDbのAPIキー
        $error = "";
        $movieArray = "";
        if (array_key_exists('movie_title', $_GET) && $_GET['movie_title'] != "") {
            // file_get_contents("https://api.themoviedb.org/3/discover/movie?api_key=". $apikey ."&with_genres=27");
            $url_Contents =  file_get_contents("https://api.themoviedb.org/3/search/movie?api_key=" . $apikey . "&language=ja-JA&query=" . $_GET['movie_title'] . "&page=5&include_adult=false");
                
            $movieArray = json_decode($url_Contents, true);
            $i =0;
            foreach ($movieArray['results'] as $record) {
                $id = $record['id'];
                $tmp = file_get_contents("https://api.themoviedb.org/3/movie/" . $id . "?api_key=" . $apikey . "&language=en-US");
                $detail =  json_decode($tmp, true);
                if ((int)$detail['runtime'] < 100) {

                    unset($movieArray['results'][$i]);
                }
                $i++;
            }
        }
        return view('index', ['movieArray' => $movieArray, 'error' => $error]);
    }
}
