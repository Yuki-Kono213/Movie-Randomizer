<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Pool;
use Psr\Http\Message\ResponseInterface;


class APIController extends Controller
{
    function index()
    {

        // インスタンス作成
        $client = new Client();
        // $client = new Client(['debug' => true]); //通信内容をデバッグしたい場合
        $apikey = "e9678255150ea732f1e1c718fd75ed6d"; //TMDbのAPIキー
        $error = "";
        $movieArray = [];
        $bodies = [];
        $minimum_time = 0;
        $max_time = 180;
        $minimum_age = 1990;
        $max_age = date('Y');
        $minimum_vote = 70;
        $max_vote = 100;
        $movieData = null;
        $imgtxt = [];
        $count = 0;
        if (array_key_exists('movie_title', $_GET) && $_GET['movie_title'] != "") {
            $url_Contents = [];
            if (array_key_exists('minimum_time', $_GET) && $_GET['minimum_time'] != "") {
                $minimum_time = $_GET['minimum_time'];
            }

            if (array_key_exists('max_time', $_GET) && $_GET['max_time'] != "") {
                $max_time = $_GET['max_time'];
            }
            if (array_key_exists('minimum_age', $_GET) && $_GET['minimum_age'] != "") {
                $minimum_age = $_GET['minimum_age'];
            }

            if (array_key_exists('max_age', $_GET) && $_GET['max_age'] != "") {
                $max_age = $_GET['max_age'];
            }
            if (array_key_exists('minimum_vote', $_GET) && $_GET['minimum_vote'] != "") {
                $minimum_vote = $_GET['minimum_vote'];
            }

            if (array_key_exists('max_vote', $_GET) && $_GET['max_vote'] != "") {
                $max_vote = $_GET['max_vote'];
            }

            if (array_key_exists('count', $_GET) && $_GET['count'] != "") {
                $count = $_GET['count'];
            }
            // $url_Contents =   $client->request('GET',"https://api.themoviedb.org/3/discover/movie?api_key=". $apikey ."&with_genres=27");
            $url_Contents =  $client->request('GET', "https://api.themoviedb.org/3/search/movie?api_key=" . $apikey . "&language=ja-JA&query=" . $_GET['movie_title'] . "&page=1&include_adult=false");
            $PageArray = json_decode($url_Contents->getBody()->getContents(), true);
            $totalpage = $PageArray['total_pages'];
            // for ($i = 1; $i <= $totalpage; $i++) {
            //     $promises[] = $client->requestAsync('GET',"https://api.themoviedb.org/3/search/movie?api_key=" . $apikey . "&language=ja-JA&query=" . $_GET['movie_title'] . "&page=" . (string)$i . "&include_adult=false");
            //     // $movieArray[] = json_decode($url_Contents->getBody()->getContents(), true);
            // }
            $firstrequests = function () use ($client, $totalpage, $apikey) {
                for ($i = 1; $i <= $totalpage; $i++) {
                    yield function () use ($client, $apikey, $i) {
                        return $client->requestAsync('GET', "https://api.themoviedb.org/3/search/movie?api_key=" . $apikey . "&language=ja-JA&query=" . $_GET['movie_title'] . "&page=" . (string)$i . "&include_adult=false");
                    };
                }
            };
            $randArray = $this->totalPageRandomizer(1,$totalpage);
            $firstpool = new Pool($client, $firstrequests(), [
                'concurrency' => 25,
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
            $requests = function ($movieArray, $totalpage, $apikey, &$find) use ($client, $max_vote, $minimum_vote, $minimum_age, $max_age, $randArray) {
                
                for ($i = 0; $i < $totalpage; $i++) {
                    $page = $randArray[$i];
                    foreach ($movieArray[$page]['results'] as $record) {
                        if ($find <= 5 &&
                            $record['vote_average'] * 10 <= $max_vote && $record['vote_average'] * 10 >= $minimum_vote
                            && (array_key_exists('release_date', $record)) && date('Y', strtotime($record['release_date'])) <= $max_age && date('Y', strtotime($record['release_date'])) >= $minimum_age
                        ) {
                            var_dump($find);
                            yield function () use ($client, $record, $apikey) {
                                return $client->requestAsync('GET', "https://api.themoviedb.org/3/movie/" . $record['id'] . "?api_key=" . $apikey . "&language=ja-JA",);
                            };
                        }
                    }
                }
            };

            $pool = new Pool($client, $requests($movieArray, $totalpage, $apikey, $find), [
                'concurrency' => 25,
                'fulfilled' => function (ResponseInterface $response, $index) use (&$bodies, $minimum_time, $max_time, &$find) {
                    if ($response != null) {
                        $contents = $response->getBody()->getContents();
                        $pageArray = json_decode((string)$contents, true);
                        if ($pageArray['runtime'] <= $max_time && $pageArray['runtime'] >= $minimum_time) {
                            $find++;
                            $bodies[] = $pageArray;
                            
                        }
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
            if(count($bodies) > 0){
                $rndArray = $this->totalPageRandomizer(0,$count);
                for($i = 0; $i < $count; $i++)
                {
                    $movieData[] = $bodies[$rndArray[$i]];
                    if(isset($bodies[$rndArray[$i]]['poster_path'])){
                        $data = file_get_contents("https://image.tmdb.org/t/p/w185".$bodies[$rndArray[$i]]['poster_path']);
                        $enc_img = base64_encode($data);
        
                        $imginfo = getimagesize('data:application/octet-stream;base64,' . $enc_img);
        
                        $imgtxt[] = '<img src="data:' . $imginfo['mime'] . ';base64,'.$enc_img.'">';
                    }
                    else{
                        $imgtxt[] = "";
                    }
                }
            }
            else
            {
                $movieData =null;
            }
        }
        return view('index', [
            'movieArray' => $bodies, 'error' => $error, 'minimum_time' => $minimum_time, 'max_time' => $max_time, 'minimum_age' => $minimum_age, 'max_age' => $max_age, 'minimum_vote' => $minimum_vote, 'max_vote' => $max_vote
            ,'movieData' => $movieData, 'imgtxt' => $imgtxt, 'count' => $count
        ]);
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
