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


class APIController extends Controller
{
    public function RoutingFunc(Request $request)
    {
        $array = [];
        if ($request->has('btn-MyMovie')) {
           $array = $this->WatchedMovieView();
        } 
        else
        {
            $array = $this->index();

        }
        
        return view( 'index',[
            'error' => $array['error'], 'movieData' => $array['movieData'], 'imgtxt' => $array['imgtxt'], 'explain' => $array['explain'],
            'selectedvalue' => $array['selectedvalue'], 'genreArray' => $array['genreArray'], 'config' => $array['config'], 'user' => $array['user'],
            'rate' => $array['rate']
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
        $movieArray = [];
        $bodies = [];
        $explain = [];
        $config = [];
        $rate = [];
        $movieData = [];
        $imgtxt = [];
        $genres = [];
        $selectedvalue = $this->InitializedValue();
        $genresArray = $this->ArrayReturn();
        $config = $this->GetConfigData();
        if (array_key_exists('movie_title', $_GET) || array_key_exists('genre0', $_GET) || array_key_exists('genre1', $_GET)  || array_key_exists('genre2', $_GET)) {
            $url_Contents = [];

            $genres = [];
            for ($i = 0; $i < 3; $i++) {
                if (array_key_exists('genre' . $i, $_GET) && $_GET['genre' . $i] != 0) {
                    $genres[] = $_GET['genre' . $i];
                    $selectedvalue[$i] = $_GET['genre' . $i];
                }
            }
            // $url_Contents =   $client->request('GET',"https://api.themoviedb.org/3/discover/movie?api_key=". $apikey ."&with_genres=27");
            $url_Contents =  $client->request('GET', $this->ReturnMovieData($apikey, $genres, $config, 1));
            $PageArray = json_decode($url_Contents->getBody()->getContents(), true);
            $totalpage = $PageArray['total_pages'];
            // for ($i = 1; $i <= $totalpage; $i++) {
            //     $promises[] = $client->requestAsync('GET',"https://api.themoviedb.org/3/search/movie?api_key=" . $apikey . "&language=ja-JA&query=" . $_GET['movie_title'] . "&page=" . (string)$i . "&include_adult=false");
            //     // $movieArray[] = json_decode($url_Contents->getBody()->getContents(), true);
            // }
            $firstrequests = function () use ($client, $totalpage, $apikey, $genres, $config) {
                for ($i = 1; $i <= $totalpage; $i++) {
                    yield function () use ($client, $apikey, $i,  $genres, $config) {
                        return $client->requestAsync('GET', $this->ReturnMovieData($apikey, $genres, $config, $i));
                    };
                }
            };
            $randArray = $this->totalPageRandomizer(1, $totalpage);
            $firstpool = new Pool($client, $firstrequests(),  [
                'concurrency' => $_GET['count'],
                'fulfilled' => function (ResponseInterface $response, $index) use (&$movieArray) {
                    $contents = $response->getBody()->getContents();
                    $pageArray = json_decode((string)$contents, true);
                    $movieArray[$pageArray['page']] = $pageArray;
                },
                'rejected' => function ($reason, $index) {
                    var_dump("ng");
                },
            ]);
            $promise = $firstpool->promise();
            $promise->wait();
            $find = 0;
            $requests = function ($movieArray, $totalpage, $apikey, &$find) use ($client, $config, $randArray, $genres) {

                for ($i = 0; $i < $totalpage; $i++) {
                    $page = $randArray[$i];
                    foreach ($movieArray[$page]['results'] as $record) {
                        if ($find <= $config['count']) {
                            yield function () use ($client, $record, $apikey) {
                                return $client->requestAsync('GET', "https://api.themoviedb.org/3/movie/" . $record['id'] . "?api_key=" . $apikey . "&language=ja-JA",);
                            };
                        }
                    }
                }
            };

            $pool = new Pool($client, $requests($movieArray, $totalpage, $apikey, $find), [
                'concurrency' => $_GET['count'],
                'fulfilled' => function (ResponseInterface $response, $index) use (&$bodies, &$explain, $config, &$find, $client, $apikey) {
                    if ($response != null) {
                        $contents = $response->getBody()->getContents();
                        $pageArray = json_decode((string)$contents, true);
                        $find++;
                        $bodies[] = $pageArray;
                        
                    }
                },
                'rejected' => function ($reason, $index) {
                },
            ]);
            $promise = $pool->promise();
            $movieArray = $bodies;
            $promise->wait();
            // $responses = $promise;
            // foreach ($responses as $response) {

            //     $body = json_decode($response->getBody()->getContents());
            //     dd($body);
            //     if ($body['runtime'] < 80) {
            //         $bodies[] = $body;
            //     }
            // }
            if (count($bodies) > 0) {
                $rndArray = $this->totalPageRandomizer(0, count($bodies) - 1);

                for ($i = 0; $i < $config['count']; $i++) {
                    if ($i == count($bodies)) {
                        break;
                    }
                    $movieData[] = $bodies[$rndArray[$i]];
                    $response = $client->request('GET', "https://api.themoviedb.org/3/search/movie?api_key=" . $apikey . "&language=ja-JA&query=" . $bodies[$rndArray[$i]]['title'] . "&page=1&include_adult=false");

                    $contents = $response->getBody()->getContents();
                    $contents = json_decode((string)$contents, true);


                    if (count($contents['results']) > 1) {
                        for ($j = 0; $j < count($contents['results']); $j++) {
                            if ($contents['results'][$j]['id'] == $bodies[$rndArray[$i]]['id']) {
                                if ($user != null && Watched_Movie::alreadyWatchedMovie($user->id, $contents['results'][$j]['id'])) {
                                    $rate[$contents['results'][$j]['id']] = Watched_Movie::alreadyWatchedMovieRate($user->id, $contents['results'][$j]['id']);
                                }
                                $contents = $contents['results'][$j];
                                break;
                            }
                        }
                    } else {
                        $contents = $contents['results'][0];
                    }
                    $explain[] = $contents;
                    if (isset($movieData[$i]['poster_path'])) {
                        $data = file_get_contents("https://image.tmdb.org/t/p/w185" . $movieData[$i]['poster_path']);
                        $enc_img = base64_encode($data);

                        $imginfo = getimagesize('data:application/octet-stream;base64,' . $enc_img);

                        $imgtxt[] = '<img src="data:' . $imginfo['mime'] . ';base64,' . $enc_img . '">';
                    } else {
                        $imgtxt[] = '<img src="/img/noimage.png">';
                    }
                }
            } else {
                $movieData[] = "なし";
            }
        }
        return [
            'error' => $error, 'movieData' => $movieData, 'imgtxt' => $imgtxt, 'explain' => $explain,
            'selectedvalue' => $selectedvalue, 'genreArray' => $genresArray, 'config' => $config, 'user' => $user,
            'rate' => $rate
        ];
    }

    function ReturnMovieData($apikey, $genres, $config, $page)
    {
        $string = null;
        if (array_key_exists('movie_title', $_GET) && $_GET['movie_title'] != "") {
            $string =  "https://api.themoviedb.org/3/search/movie?api_key=" . $apikey . "&language=ja-JA&query=" . $_GET['movie_title'] . "&page=". $page ."&include_adult=false&with_runtime.gte=". $config['minimum_time']."&with_runtime.lte="
            . $config['max_time']."&vote_average.lte=". $config['max_vote']."&vote_average.gte=". $config['minimum_vote']."&vote_count.gte=". $config['min_vote_count']
            . "&primary_release_date.lte=". $config['max_age']."&primary_release_date.gte=". $config['minimum_age'];
            
        } else {
            $string = "https://api.themoviedb.org/3/discover/movie?api_key=" . $apikey  . "&page=". $page ."&with_runtime.gte=". $config['minimum_time']."&with_runtime.lte=". $config['max_time']."&vote_average.lte=". 
            $config['max_vote']."&vote_average.gte=". $config['minimum_vote']."&vote_count.gte=". $config['min_vote_count']
            . "&primary_release_date.lte=". $config['max_age']."&primary_release_date.gte=". $config['minimum_age'];
        }

        foreach($genres as $genre)
        {
            $string = $string. "&with_genres=" . $genre;
        }
        return $string;
    }

    function GetConfigData()
    {
        $config = [];
        
        $config['minimum_time'] = 0;
        $config['max_time']  = 180;
        $config['minimum_age']  = 1990;
        $config['max_age']  = date('Y');
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
        if (array_key_exists('minimum_age', $_GET) && $_GET['minimum_age'] != "") {
            $config['minimum_age'] = $_GET['minimum_age'];
        }

        if (array_key_exists('max_age', $_GET) && $_GET['max_age'] != "") {
            $config['max_age'] = $_GET['max_age'];
        }
        if (array_key_exists('minimum_vote', $_GET) && $_GET['minimum_vote'] != "") {
            $config['minimum_vote'] = $_GET['minimum_vote']/10;
        }

        if (array_key_exists('max_vote', $_GET) && $_GET['max_vote'] != "") {
            $config['max_vote'] = $_GET['max_vote']/10;
        }

        if (array_key_exists('min_vote_count', $_GET) && $_GET['min_vote_count'] != "") {
            $config['min_vote_count'] = $_GET['min_vote_count'];
        }
        if (array_key_exists('count', $_GET) && $_GET['count'] != "") {
            $config['count'] = $_GET['count'];
        }

        return $config;
    }

    function WatchedMovieView()
    {   
         $error = null;
        // 現在認証しているユーザーを取得
        $user = Auth::user();
        $client = new Client();
        // $client = new Client(['debug' => true]); //通信内容をデバッグしたい場合
        $apikey = "e9678255150ea732f1e1c718fd75ed6d"; //TMDbのAPIキー
        $config = $this->GetConfigData();
        $genres = [];
        $rate = [];
        $selectedvalue = [];
        $genresArray = $this->ArrayReturn();
        for ($i = 0; $i < 3; $i++) {
            if (array_key_exists('genre' . $i, $_GET) && $_GET['genre' . $i] != 0) {
                $genres[] = $_GET['genre' . $i];
                $selectedvalue[$i] = $_GET['genre' . $i];
            }
        }
        $movies = Watched_Movie::getWatchedMovie($user->id);

        $i = 0;
        foreach ($movies as $movie) {

            
            $contents = $client->request('GET', "https://api.themoviedb.org/3/movie/" . $movie->movie_id . "?api_key=" . $apikey . "&language=ja-JA", ['http_errors' => false])->getBody()->getContents();
            
            $pageArray = json_decode((string)$contents, true);
            if(!array_key_exists('success',$pageArray)){
                $movieData[] = $pageArray;
                $response = $client->request('GET', "https://api.themoviedb.org/3/search/movie?api_key=" . $apikey . "&language=ja-JA&query=" .$pageArray['title']  . "&page=1&include_adult=false");

                $contents = $response->getBody()->getContents();
                $contents = json_decode((string)$contents, true);


                $rate[$movie->movie_id] = $movie->movie_rate;
                            
                if (count($contents['results']) > 1) {
                    for ($j = 0; $j < count($contents['results']); $j++) {
                        if ($contents['results'][$j]['id'] == $movie->movie_id) {
                            $contents = $contents['results'][$j];
                            break;
                        }
                    }
                } else {
                    $contents = $contents['results'][0];
                }
                $explain[] = $contents;
                if (isset($pageArray['poster_path'])) {
                    $data = file_get_contents("https://image.tmdb.org/t/p/w185" .$pageArray['poster_path']);
                    $enc_img = base64_encode($data);

                    $imginfo = getimagesize('data:application/octet-stream;base64,' . $enc_img);

                    $imgtxt[] = '<img src="data:' . $imginfo['mime'] . ';base64,' . $enc_img . '">';
                } else {
                    $imgtxt[] = '<img src="/img/noimage.png">';
                }
            }
            else{

            }
        }
        return [
            'error' => $error, 'movieData' => $movieData, 'imgtxt' => $imgtxt, 'explain' => $explain,
            'selectedvalue' => $selectedvalue, 'genreArray' => $genresArray, 'config' => $config, 'user' => $user,
            'rate' => $rate
        ];
    }

    function ArrayReturn()
    {
        $Array = [];
        $Array[0] = "指定なし";
        $Array[28] = "アクション";
        $Array[12] = "アドベンチャー";
        $Array[16] = "アニメーション";
        $Array[35] = "コメディ";
        $Array[80] = "犯罪";
        $Array[99] = "ドキュメンタリー";
        $Array[18] = "ドラマ";
        $Array[10751] = "ファミリー";
        $Array[14] = "ファンタジー";
        $Array[36] = "歴史";
        $Array[27] = "ホラー";
        $Array[10402] = "ミュージック";
        $Array[9648] = "ミステリー";
        $Array[10749] = "ロマンス";
        $Array[878] = "SF";
        $Array[10770] = "TV映画";
        $Array[53] = "スリラー";
        $Array[10752] = "戦争";
        $Array[37] = "西部劇";

        return $Array;
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
        return;
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
