<!DOCTYPE html>
<html lang="ja">

<head>
    <!-- Required meta tags always come first -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title>movie Search</title>
    <!-- Bootstrap CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>

<body>

    <div class="container">
        <div class="article">

            <div class="side">
                <h1>What's The movie?</h1>



                <form>
                    <fieldset class="form-group">
                        <label for="movie_title">Enter the name of a title.</label>
                        <input type="text" class="form-control" name="movie_title" id="movie_title" placeholder="映画のタイトル"
                            value="<?php
                            
                            if (array_key_exists('movie_title', $_GET)) {
                                echo $_GET['movie_title'];
                            }
                            
                            ?>">
                            <div>上映時間</div>
                        <input type="text" class="form-control" name="minimum_time" id="minimum_time" placeholder="最小上映時間"
                            value={{ $config['minimum_time'] }}>
                        <input type="text" class="form-control" name="max_time" id="max_time" placeholder="最長上映時間"
                            value={{ $config['max_time'] }}>
                            <div>ジャンル</div>
                        @for ($i = 0; $i < 3; $i++)
                            <select id="genre{{$i}}" name="genre{{$i}}">
                            @foreach ( $genreArray as $key => $pref ) 
                                @if ( ! empty( $selectedvalue[$i]) ) 
                                    
                                    @if ( $key == $selectedvalue[$i] ) 
                                        <option value="{{$key}}" selected> {{$pref}} </option>;
                                    @else
                                        <option value="{{$key}}"> {{$pref}} </option>;
                                    @endif
                                @else 
                                    <option value="{{$key}}"> {{$pref}} </option>;
                                @endif
                            @endforeach
                            </select>
                        @endfor
                        <div>上映年</div>
                        <input type="text" class="form-control" name="minimum_age" id="minimum_age" placeholder="最小上映年"
                            value={{ $config['minimum_age'] }}>
                        <input type="text" class="form-control" name="max_age" id="max_age" placeholder="最大上映年"
                            value={{ $config['max_age']}}>
                        <div>評価</div>
                        <input type="text" class="form-control" name="minimum_vote" id="minimum_vote" placeholder="最小評価"
                            value={{ $config['minimum_vote'] }}>
                        <input type="text" class="form-control" name="max_vote" id="max_vote" placeholder="最大評価"
                            value={{ $config['max_vote'] }}>
                        <div>レビュー投稿数</div> 
                        <input type="text" class="form-control" name="min_vote_count" id="min_vote_count" placeholder="最小レビュー数"
                            value={{ $config['min_vote_count']}}>
                    </fieldset>
                    <div>候補数</div>
                    <select name='count'>
                        <option value='1'>1</option>
                        <option value='2'>2</option>
                        <option value='3'>3</option>
                        <option value='4'>4</option>
                        <option value='5'>5</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Submit</button>

                    <div>
                        この製品は、TMDB APIを使用しますが、TMDBによって承認または認定されていません。
                    </div>
                </form>


            </div>
            <div class="content">
                <div id="movie">
                    
                    @if ($movieData != null && $movieData[0] != "なし") 
                        @for($i = 0; $i < count($movieData); $i++)
                        <?php 
                            echo $imgtxt[$i];
                        ?>

                        <div class="alert alert-success" role="alert">{{$explain[$i]['results'][0]['title']}}</div>
                        <span class="alert alert-success" role="alert">平均評価{{$explain[$i]['results'][0]['vote_average']}}</span>
                        <span class="alert alert-success" role="alert">上映年{{$explain[$i]['results'][0]['release_date']}}</span>
                        <div class="alert alert-success" role="alert">上映時間{{$movieData[$i]['runtime']}}分</div>
                        <span class="alert alert-success" role="alert">評価数{{$explain[$i]['results'][0]['vote_count']}}</span>
                        <div class="alert alert-success" role="alert">{{$explain[$i]['results'][0]['overview']}}</div>
                        @endfor
                    @elseif ($movieData != null && $movieData[0] == "なし") 
                        映画が見つかりませんでした。
                    @elseif ($error) 
                        <div class="alert alert-danger" role="alert">
                            {{$error}} 
                        </div>;
                    @endif

                    
                </div>
            </div>
    </div>
    </div>

    <!-- jQuery first, then Bootstrap JS. -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/js/bootstrap.min.js"
        integrity="sha384-vZ2WRJMwsjRMW/8U7i6PWi6AlO1L79snBrmgiDpgIWJ82z8eA5lenwvxbMV1PAh7" crossorigin="anonymous">
    </script>
</body>

</html>
