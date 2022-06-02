<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Pool;
use phpDocumentor\Reflection\Types\Context;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Support\Facades\Auth;
use App\Models\Watched_Movie;
use Illuminate\Support\Facades\DB;
use stdClass;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Type\Integer;

class APIController extends Controller
{

    public function RoutingFunc(Request $request)
    {
        if ($request->has('btn-Renew')) {

            $this->RenewDataBase();

            if ($request->input('order') == 'rate') {
                return view('watched_movie', [
                    'movies' => $this->PassSort_rate()
                ]);
            }
        }
        elseif ($request->has('btn-Delete')) {

            $this->DeleteDataBase();

            if ($request->input('order') == 'rate') {
                return view('watched_movie', [
                    'movies' => $this->PassSort_rate()
                ]);
            }
        }
        return view('/watched_movie', [
            'movies' => $this->PassSort_updated()
        ]);
    }
    public function Sort_rate()
    {

        return view('watched_movie', [
            'movies' => $this->PassSort_rate()
        ]);
    }
    public function PassSort_rate()
    {
        $movies = $this->WatchedMovieView_sortbyrate();
        $movies = $this->GetMovieExplain($movies);
        $movies->sort_order = "rate";
        return $movies;
    }
    public function Sort_updated()
    {
        $movies = $this->PassSort_updated();
        if(count($movies) > 0){
            return view('watched_movie', [
                'movies' => $movies
            ]);
        }
        else
        {    
            return redirect('/');
        }
    }
    public function PassSort_updated()
    {
        $movies = $this->WatchedMovieView();
        $movies = $this->GetMovieExplain($movies);

        $movies->sort_order = 'updated';
        return $movies;
    }
    public function PostWatchedMovieView(Request $request)
    {
        $watchmovies = $request->input('category');
        foreach ($watchmovies as $watchmovie) {
            $this->AddDataBase($watchmovie);
        }
        $movies = $this->WatchedMovieView();
        $movies = $this->GetMovieExplain($movies);
        return redirect('watched_movie/updated')->with([
            'movies' => $movies
        ]);
    }

    function index()
    {

        // 現在認証しているユーザーを取得
        $user = Auth::user();
        // インスタンス作成
        $client = new Client();
        // $client = new Client(['debug' => true]); //通信内容をデバッグしたい場合
        $apikey = "e9678255150ea732f1e1c718fd75ed6d"; //TMDbのAPIキー
        $error = "";
        $moviearray = [];
        $bodies = [];
        $explain = [];
        $config = [];
        $rate = [];
        $genres = [];
        $selectedvalue = $this->InitializedValue();
        $genresArray = $this->ArrayReturn();
        $config = $this->GetConfigData();

        $movies = null;
        if ($this->inputExist()) {

            $url_Contents = [];
            $genres = [];
            for ($i = 0; $i < 3; $i++) {
                if (array_key_exists('genre' . $i, $_GET) && $_GET['genre' . $i] != 0) {
                    $genres[] = $_GET['genre' . $i];
                    $selectedvalue[$i] = $_GET['genre' . $i];
                }
            }
            
        if(!$config['exist'])
        {
            return view('index', [
                'error' => $error, 'movies' => null,
                'selectedvalue' => $selectedvalue, 'genreArray' => $genresArray, 'config' => $config, 'user' => $user
            ]);
        }
            $url_Contents =  $client->request('GET', $this->ReturnMovieData($apikey, $genres, $config, 1));
            $pagearray = json_decode($url_Contents->getBody()->getContents(), true);
            $totalResults = $pagearray['total_results'];

            $pagernd = [];

            if ($totalResults == 0 ) {
                $movies = null;

                $config['minimum_vote'] = $_GET['minimum_vote'];
                $config['max_vote'] = $_GET['max_vote'];

                return [
                    'error' => $error, 'explain' => $explain,
                    'selectedvalue' => $selectedvalue, 'genreArray' => $genresArray, 'config' => $config, 'user' => $user,
                    'rate' => $rate
                ];
            }
            $pagernd = $this->totalResultsRandomizer(0, $totalResults - 1);
            $firstrequests = function () use ($client, $apikey, $genres, $config, $pagernd) {
                for ($i = 1; $i <=  $_GET['count']; $i++) {
                    if ($i <= count($pagernd)) {
                        yield function () use ($client, $apikey, $i,  $genres, $config, $pagernd) {
                            return $client->requestAsync('GET', $this->ReturnMovieData($apikey, $genres, $config, (((int)$pagernd[$i - 1] + 20) / 20)));
                        };
                    }
                }
            };
            $firstpool = new Pool($client, $firstrequests(5),  [
                'concurrency' => $_GET['count'],
                'fulfilled' => function (ResponseInterface $response) use (&$moviearray) {
                    $contents = $response->getBody()->getContents();
                    $pagearray = json_decode((string)$contents, true);
                    $moviearray[$pagearray['page'] - 1] = $pagearray;
                },
                'rejected' => function () {
                    var_dump("ng");
                },
            ]);
            $promise = $firstpool->promise();
            $promise->wait();
            $find = 0;
            $movies = [];
            $requests = function ($total) use ($client, $config, $pagernd, $moviearray, $apikey, &$find) {

                for ($i = 0; $i < $total; $i++) {
                    if ($i < count($pagernd) && $find < $config['count'] && $find < $total && $i < $total) {
                        $page = $pagernd[$i];
                        yield function () use ($client, $apikey, $moviearray, $page) {
                            return $client->requestAsync('GET', "https://api.themoviedb.org/3/movie/" . $moviearray[$page / 20]['results'][$page % 20]['id'] . "?api_key=" . $apikey . "&language=ja-JA");
                        };
                    }
                }
            };

            $pool = new Pool($client, $requests($_GET['count']), [
                'concurrency' => $_GET['count'],
                'fulfilled' => function (ResponseInterface $response) use (&$bodies, &$find) {
                    if ($response != null) {
                        $contents = $response->getBody()->getContents();
                        $pagearray = json_decode((string)$contents, true);
                        $find++;
                        $bodies[] = $pagearray;
                    }
                },
                'rejected' => function () {
                    var_dump("ng");
                },
            ]);
            $promise = $pool->promise();
            $moviearray = $bodies;
            $promise->wait();

            //var_dump($time = microtime(true) - $time_start);
            if (count($bodies) > 0) {
                for ($i = 0; $i < count($bodies); $i++) {
                    $movies[$i] = new Watched_Movie(['movie_id' => $bodies[$i]['id']]);
                }
                $movies = $this->GetMovieExplain($movies);
                $config['exist'] = true;
            } else {
                $movies = null;
                $config['exist'] = false;
            }
            //var_dump($time = microtime(true) - $time_start);
        } else if (isset($_GET['push'])) {
            $config['exist'] = false;
        }
        return view('index', [
            'error' => $error,  'movies' => $movies,
            'selectedvalue' => $selectedvalue, 'genreArray' => $genresArray, 'config' => $config, 'user' => $user
        ]);
    }

    function GetMovieExplain($movies)
    {
        $user = Auth::user();
        $client = new Client();
        $apikey = "e9678255150ea732f1e1c718fd75ed6d"; //TMDbのAPIキー
        $requests = function ($total) use ($client, $apikey, $movies) {
            foreach ($movies as $movie) {
                yield function () use ($client, $movie, $apikey) {

                    return $client->requestAsync('GET', "https://api.themoviedb.org/3/movie/" . $movie->movie_id . "?api_key=" . $apikey . "&language=ja&page=1&include_adult=false");
                };
            }
        };
        $explain = [];
        $pool = new Pool($client, $requests(5), [
            'concurrency' => 5,
            'fulfilled' => function (ResponseInterface $response) use (&$explain, $user) {
                if ($response != null) {
                    $response = $response->getBody()->getContents();
                    $contents = json_decode((string)$response, true);
                    $explain[] = $contents;
                }
            },
            'rejected' => function () {
                var_dump("ng");
            },
        ]);
        $promise = $pool->promise();
        $promise->wait();
        $requests = function ($total) use ($client, $explain) {
            for ($i = 0; $i < $total; $i++) {
                if (isset($explain[$i]['poster_path'])) {
                    yield function () use ($client, $explain, $i) {

                        return $client->requestAsync('GET', "https://image.tmdb.org/t/p/w185" . $explain[$i]['poster_path']);
                    };
                } else {
                    $explain[$i]['imgtxt'] = '<img src="/img/noimage.png">';
                }
            }
        };
        $pool = new Pool($client, $requests(5), [
            'concurrency' => 5,
            'fulfilled' => function (ResponseInterface $response, $i) use (&$explain, $user) {
                if ($response != null) {
                    $response = $response->getBody()->getContents();
                    $enc_img = base64_encode($response);
                    $imginfo = getimagesize('data:application/octet-stream;base64,' . $enc_img);
                    $explain[$i]['imgtxt'] = '<img src="data:' . $imginfo['mime'] . ';base64,' . $enc_img . '">';
                }
            },
            'rejected' => function () {
                var_dump("ng");
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();
        foreach ($movies as $movie) {
            for ($i = 0; $i < count($explain); $i++) {
                if ($movie->movie_id == $explain[$i]['id']) {
                    $movie->explain = $explain[$i];
                    break;
                }
            }
        }

        return $movies;
    }

    function ReturnMovieData($apikey, $genres, $config, $page)
    {
        $string = null;
        $string = "https://api.themoviedb.org/3/discover/movie?api_key=" . $apikey  . "&page=" . $page . "&with_runtime.gte=" . $config['minimum_time'] . "&with_runtime.lte=" . $config['max_time'] . "&vote_average.lte=" .
            ($config['max_vote'] / 10) . "&vote_average.gte=" . ($config['minimum_vote'] / 10) . "&vote_count.gte=" . $config['min_vote_count']
            . "&primary_release_date.lte=" . $config['max_age'] . "&primary_release_date.gte=" . $config['minimum_age'];
        $i = 0;
        foreach ($genres as $genre) {
            if ($i === 0) {
                $string = $string . "&with_genres=" . $genre;
                $i++;
            } else {
                $string = $string . "," . $genre;
            }
        }
        return $string;
    }

    function GetConfigData()
    {
        $config = [];

        $config['minimum_time'] = 0;
        $config['max_time']  = 180;
        $config['minimum_age']  = 1990;
        $config['max_age']  = date('Y') + 1;
        $config['minimum_vote']  = 70;
        $config['max_vote'] = 100;
        $config['min_vote_count'] = 100;
        $config['count'] = 5;
        if (array_key_exists('minimum_time', $_GET) && $_GET['minimum_time'] != "") {
            $config['minimum_time'] = $_GET['minimum_time'];
        }

        if (array_key_exists('max_time', $_GET) && $_GET['max_time'] != "") {
            $config['max_time'] = $_GET['max_time'];
        }
        if (array_key_exists('minimum_age', $_GET) && $_GET['minimum_age'] != "" ) {
            $config['minimum_age'] = $_GET['minimum_age'];
        }

        if (array_key_exists('max_age', $_GET) && $_GET['max_age'] != "" && is_numeric( $_GET['max_age'] )) {
            $config['max_age'] = $_GET['max_age'] + 1;
        }
        if (array_key_exists('minimum_vote', $_GET) && $_GET['minimum_vote'] != "") {
            $config['minimum_vote'] = $_GET['minimum_vote'];
        }

        if (array_key_exists('max_vote', $_GET) && $_GET['max_vote'] != "") {
            $config['max_vote'] = $_GET['max_vote'];
        }

        if (array_key_exists('min_vote_count', $_GET) && $_GET['min_vote_count'] != "") {
            $config['min_vote_count'] = $_GET['min_vote_count'];
        }
        if (array_key_exists('count', $_GET) && $_GET['count'] != "") {
            $config['count'] = $_GET['count'];
        }
        if($config['max_age'] == $config['minimum_age'] || !is_numeric($config['max_age']) || !is_numeric(($config['minimum_age'])))
        {
            $config['exist'] = false;

        }
        else if($config['max_vote'] == $config['minimum_vote'] || !is_numeric($config['max_vote']) || !is_numeric($config['minimum_vote']) )
        {
            $config['exist'] = false;
        }
        else if($config['max_time'] == $config['minimum_time'] || !is_numeric($config['max_time'])  || !is_numeric($config['minimum_time']) )
        {
            $config['exist'] = false;
        }
        else if(!is_numeric($config['min_vote_count']))
        {
            $config['exist'] = false;
        }
        else
        {
            $config['exist'] = true;

        }
        return $config;
    }

    function inputExist()
    {
        // if((array_key_exists('movie_title', $_GET) && ($_GET['movie_title'] != "" )) || 
        if ((array_key_exists('genre0', $_GET) && ($_GET['genre0'] != "0"))  ||
            (array_key_exists('genre1', $_GET) && ($_GET['genre1'] != "0"))   ||
            (array_key_exists('genre2', $_GET)) && ($_GET['genre2'] != "0")
        ) {
            return true;
        }

        return false;
    }

    function AddDataBase($movie)
    {
        $user = Auth::user();
        Watched_Movie::addWatchedMovieRate($user->id, $movie, 0);
    }

    function RenewDataBase()
    {
        $user = Auth::user();
        Watched_Movie::renewWatchedMovieRate($user->id, $_POST['movie_id'], $_POST['rate']);
    }
    function DeleteDataBase()
    {
        $user = Auth::user();
        Watched_Movie::DeleteWatchedMovie($user->id, $_POST['movie_id']);
    }
    function WatchedMovieView()
    {

        $user = Auth::user();
        $movies = Watched_Movie::getWatchedMovie($user->id);
        return $movies;
    }

    function WatchedMovieView_sortbyrate()
    {

        $user = Auth::user();
        $movies = Watched_Movie::getWatchedMovie_sortbyrate($user->id);
        return $movies;
    }

    function ArrayReturn()
    {
        $array = [];
        $array[0] = "指定なし";
        $array[28] = "アクション";
        $array[12] = "アドベンチャー";
        $array[16] = "アニメーション";
        $array[35] = "コメディ";
        $array[80] = "犯罪";
        $array[99] = "ドキュメンタリー";
        $array[18] = "ドラマ";
        $array[10751] = "ファミリー";
        $array[14] = "ファンタジー";
        $array[36] = "歴史";
        $array[27] = "ホラー";
        $array[10402] = "ミュージック";
        $array[9648] = "ミステリー";
        $array[10749] = "ロマンス";
        $array[878] = "SF";
        $array[10770] = "TV映画";
        $array[53] = "スリラー";
        $array[10752] = "戦争";
        $array[37] = "西部劇";

        return $array;
    }
    function ReturnSelectedIndex($value)
    {
        if ($value == 0) {
            return 0;
        }
        if ($value == 28) {
            return 1;
        }
        if ($value == 12) {
            return 2;
        }
        if ($value == 16) {
            return 3;
        }
        if ($value == 35) {
            return 4;
        }
        if ($value == 80) {
            return 5;
        }
        if ($value == 99) {
            return 6;
        }
        if ($value == 18) {
            return 7;
        }
        if ($value == 10751) {
            return 8;
        }
        if ($value == 14) {
            return 9;
        }
        if ($value == 36) {
            return 10;
        }
        if ($value == 27) {
            return 11;
        }
        if ($value == 10402) {
            return 12;
        }
        if ($value == 9648) {
            return 13;
        }
        if ($value == 10749) {
            return 14;
        }
        if ($value == 878) {
            return 15;
        }
        if ($value == 10770) {
            return 16;
        }
        if ($value == 53) {
            return 17;
        }
        if ($value == 10752) {
            return 18;
        }
        if ($value == 37) {
            return 19;
        }
    }
    function InitializedValue()
    {
        $array = [];
        $array[] = 0;
        $array[] = 0;
        $array[] = 0;
        return $array;
    }
    function totalResultsRandomizer($min, $totalresults)
    {
        /** 乱数用配列 */
        $rands = [];
        /** 乱数の範囲は1～10 */
        $max = $totalresults;

        for ($i = $min; $i <= $max; $i++) {
            while (true) {
                /** 一時的な乱数を作成 */
                $tmp = mt_rand($min, $max);

                /*
     * 乱数配列に含まれているならwhile続行、 
     * 含まれてないなら配列に代入してbreak 
     */
                if (!in_array($tmp, $rands)) {
                    array_push($rands, $tmp);
                    break;
                }
            }
        }

        return $rands;
    }

    function totalPageRandomizer($min, $totalpage)
    {
        /** 乱数用配列 */
        $rands = [];
        /** 乱数の範囲は1～10 */
        $max = $totalpage;

        for ($i = $min; $i <= $max; $i++) {
            while (true) {
                /** 一時的な乱数を作成 */
                $tmp = mt_rand($min, $max);

                /*
     * 乱数配列に含まれているならwhile続行、 
     * 含まれてないなら配列に代入してbreak 
     */
                if (!in_array($tmp, $rands)) {
                    array_push($rands, $tmp);
                    break;
                }
            }
        }

        return $rands;
    }
}
