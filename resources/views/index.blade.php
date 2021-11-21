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
                            value={{ $minimum_time }}>
                        <input type="text" class="form-control" name="max_time" id="max_time" placeholder="最長上映時間"
                            value={{ $max_time }}>
                            <div>ジャンル</div>
                        @for ($i = 0; $i < 3; $i++)
                            <select name='age'>
                                <option value='0'>指定なし</option>
                                <option value='28'>アクション</option>
                                <option value='12'>アドベンチャー</option>
                                <option value='16'>アニメーション</option>
                                <option value='35'>コメディ</option>
                                <option value='80'>犯罪</option>
                                <option value='99'>ドキュメンタリー</option>
                                <option value='18'>ドラマ</option>
                                <option value='10751'>ファミリー</option>
                                <option value='14'>ファンタジー</option>
                                <option value='36'>歴史</option>
                                <option value='27'>ホラー</option>
                                <option value='10402'>ミュージック</option>>
                                <option value='9648'>ミステリー</option>
                                <option value='10749'>ロマンス</option>
                                <option value='878'>SF</option>>
                                <option value='10770'>TV映画</option>
                                <option value='53'>スリラー</option>
                                <option value='10752'>戦争</option>
                                <option value='37'>西部劇</option>
                            </select>
                        @endfor
                        <div>上映年</div>
                        <input type="text" class="form-control" name="minimum_age" id="minimum_age" placeholder="最小上映年"
                            value={{ $minimum_age }}>
                        <input type="text" class="form-control" name="max_age" id="max_age" placeholder="最大上映年"
                            value={{ $max_age }}>
                        <div>評価</div>
                        <input type="text" class="form-control" name="minimum_vote" id="minimum_vote" placeholder="最小評価"
                            value={{ $minimum_vote }}>
                        <input type="text" class="form-control" name="max_vote" id="max_vote" placeholder="最大評価"
                            value={{ $max_vote }}>
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
                    
                    @if (count($movieArray) != 0) 
                        @for($i = 0; $i < $count; $i++)
                        <?php 
                            echo $imgtxt[$i];
                        ?>

                        <div class="alert alert-success" role="alert">{{$movieData[$i]['title']}}</div>
                        @endfor
                    @elseif ($movieArray != null) 
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
