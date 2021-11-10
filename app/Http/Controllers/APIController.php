<?php

namespace App\Http\Controllers;

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
        if (array_key_exists('movie_title', $_GET) && $_GET['movie_title'] != "") {
            $url_Contents = [];
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

            $contents = [];
            $firstpool = new Pool($client, $firstrequests(), [
                'concurrency' => 100,
                'fulfilled' => function (ResponseInterface $response, $index) use(&$movieArray) {
                    $contents = $response->getBody()->getContents();
                    $pageArray = json_decode((string)$contents,true);
                    $movieArray[$pageArray['page']] = $pageArray;
                },
                'rejected' => function ($reason, $index) {
                    var_dump("ng");
                },
            ]);
            $promise = $firstpool->promise();
            $promise->wait();
            // $responses = $promise;
            // foreach ($responses as $response) {

            //     $response = json_decode($response->getBody()->getContents(), true);
            //     $movieArray[] = $response;
            // }
            // for ($i = 1; $i <= $totalpage; $i++) {
            //     foreach ($movieArray[$i]['results'] as $record) {
            //         $promises[] = $client->requestAsync('GET',"https://api.themoviedb.org/3/movie/" . $record['id'] . "?api_key=" . $apikey . "&language=ja-JA",
            //         [ 'on_stats' => function ($stats) { dump($stats->getTransferTime()); } ]
            //         )->then(
            //             function ($res) use($i) {
            //                 dump("ok $i");
            //             },
            //             function ($res) use($i) {
            //                 dump("ng $i");
            //             });
            //         // $detail =  json_decode($tmp->getBody()->getContents(), true);
            //         // if ((int)$detail['runtime'] < 100) {

            //         //     unset($movieArray[$i]['results'][$j]);
            //         // }
            //     }

            // }
            $requests = function ($movieArray, $totalpage, $apikey) use ($client) {
                for ($i = 1; $i <= $totalpage; $i++) {
                    foreach ($movieArray[$i]['results'] as $record) {
                        yield function () use ($client, $record, $apikey) {
                            return $client->requestAsync('GET', "https://api.themoviedb.org/3/movie/" . $record['id'] . "?api_key=" . $apikey . "&language=ja-JA",);
                        };
                    }
                }
            };

            $pool = new Pool($client, $requests($movieArray, $totalpage, $apikey), [
                'concurrency' => 100,
                'fulfilled' => function (ResponseInterface $response, $index) use(&$bodies){
                    $contents = $response->getBody()->getContents();
                    $pageArray = json_decode((string)$contents,true);
                    if ($pageArray['runtime']< 80) {
                        $bodies[] = $pageArray;
                    }
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
        }
        return view('index', ['movieArray' => $bodies, 'error' => $error]);
    }
}
