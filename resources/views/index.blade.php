<!DOCTYPE html>
<html lang="ja">

<head>
    <!-- Required meta tags always come first -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title>movie Search</title>
    <!-- Bootstrap CSS -->
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">

</head>

<body>

    <div class="container">
        <div class="article">

            <div class="side">
                <h1>What's The movie?</h1>



                <form method="$_POST">
                    <fieldset class="form-group">
                        <label for="movie_title">Enter the name of a title.</label>
                        <input type="text" class="form-control" name="movie_title" id="movie_title"
                            placeholder="映画のタイトル" value="<?php

if (array_key_exists('movie_title', $_GET)) {
    echo $_GET['movie_title'];
}

?>">
                        <div>上映時間</div>
                        <input type="text" class="form-control" name="minimum_time" id="minimum_time"
                            placeholder="最小上映時間" value={{ $config['minimum_time'] }}>
                        <input type="text" class="form-control" name="max_time" id="max_time" placeholder="最長上映時間"
                            value={{ $config['max_time'] }}>
                        <div>ジャンル</div>
                        @for ($i = 0; $i < 3; $i++)
                            <select id="genre{{ $i }}" name="genre{{ $i }}">
                                @foreach ($genreArray as $key => $pref)
                                    @if (!empty($selectedvalue[$i]))

                                        @if ($key == $selectedvalue[$i])
                                            <option value="{{ $key }}" selected> {{ $pref }} </option>;
                                        @else
                                            <option value="{{ $key }}"> {{ $pref }} </option>;
                                        @endif
                                    @else
                                        <option value="{{ $key }}"> {{ $pref }} </option>;
                                    @endif
                                @endforeach
                            </select>
                        @endfor
                        <div>上映年</div>
                        <input type="text" class="form-control" name="minimum_age" id="minimum_age" placeholder="最小上映年"
                            value={{ $config['minimum_age'] }}>
                        <input type="text" class="form-control" name="max_age" id="max_age" placeholder="最大上映年"
                            value={{ $config['max_age'] }}>
                        <div>評価</div>
                        <input type="text" class="form-control" name="minimum_vote" id="minimum_vote"
                            placeholder="最小評価" value={{ $config['minimum_vote'] }}>
                        <input type="text" class="form-control" name="max_vote" id="max_vote" placeholder="最大評価"
                            value={{ $config['max_vote'] }}>
                        <div>レビュー投稿数</div>
                        <input type="text" class="form-control" name="min_vote_count" id="min_vote_count"
                            placeholder="最小レビュー数" value={{ $config['min_vote_count'] }}>
                    </fieldset>
                    <div>候補数</div>
                    <select name='count'>
                        <option value='1' <?= $config['count'] == 1 ? 'selected' : '' ?>>1</option>
                        <option value='2' <?= $config['count'] == 2 ? 'selected' : '' ?>>2</option>
                        <option value='3' <?= $config['count'] == 3 ? 'selected' : '' ?>>3</option>
                        <option value='4' <?= $config['count'] == 4 ? 'selected' : '' ?>>4</option>
                        <option value='5' <?= $config['count'] == 5 ? 'selected' : '' ?>>5</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <div>
                        @if ($user == null)
                            <a href="{{ route('login') }}"
                                class="text-sm text-gray-700 dark:text-gray-500 underline">ログイン</a>
                        @else
                            <a href="{{ route('loggedOutRoute') }}"
                                class="text-sm text-gray-700 dark:text-gray-500 underline">ログアウト</a>
                                <span>"{{$watchedMovieCount}}"</span>
                        @endif
                    </div>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">登録</a>
                    @endif

                </form>

                <div>
                    この製品は、TMDB APIを使用しますが、TMDBによって承認または認定されていません。
                </div>


            </div>
            <div class="content">
                <div id="movie">

                    @if ($movieData != null && $movieData[0] != 'なし')
                        @for ($i = 0; $i < count($movieData); $i++)
                            <?php
                            echo $imgtxt[$i];
                            ?>

                            <div class="alert alert-success" role="alert">{{ $explain[$i]['title'] }}</div>
                            <span class="alert alert-success"
                                role="alert">平均評価{{ $explain[$i]['vote_average'] }}</span>
                            <span class="alert alert-success"
                                role="alert">上映年{{ $explain[$i]['release_date'] }}</span>
                            <div class="alert alert-success" role="alert">上映時間{{ $movieData[$i]['runtime'] }}分</div>
                            <span class="alert alert-success" role="alert">評価数{{ $explain[$i]['vote_count'] }}</span>
                            @if($user != null)
                                <span class="alert alert-success" role="alert">あなたの評価
                                    @if(isset($rate[$explain[$i]['id']]))
                                    <select name='rate'>
                                        <option value='なし'>なし</option>
                                        <option value='1' <?= $rate[$explain[$i]['id']] == 1 ? 'selected' : '' ?>>1</option>
                                        <option value='2' <?= $rate[$explain[$i]['id']] == 2 ? 'selected' : '' ?>>2</option>
                                        <option value='3' <?= $rate[$explain[$i]['id']] == 3 ? 'selected' : '' ?>>3</option>
                                        <option value='4' <?= $rate[$explain[$i]['id']] == 4 ? 'selected' : '' ?>>4</option>
                                        <option value='5' <?= $rate[$explain[$i]['id']] == 5 ? 'selected' : '' ?>>5</option>
                                        <option value='6' <?= $rate[$explain[$i]['id']] == 6 ? 'selected' : '' ?>>6</option>
                                        <option value='7' <?= $rate[$explain[$i]['id']] == 7 ? 'selected' : '' ?>>7</option>
                                        <option value='8' <?= $rate[$explain[$i]['id']] == 8 ? 'selected' : '' ?>>8</option>
                                        <option value='9' <?= $rate[$explain[$i]['id']] == 9 ? 'selected' : '' ?>>9</option>
                                        <option value='10' <?= $rate[$explain[$i]['id']] == 10 ? 'selected' : '' ?>>10</option>
                                    </select>
                                    @else 
                                    <select name='rate'>
                                        <option value='なし' selected="true">なし</option>
                                        <option value='1' >1</option>
                                        <option value='2'>2</option>
                                        <option value='3'>3</option>
                                        <option value='4'>4</option>
                                        <option value='5'>5</option>
                                        <option value='6'>6</option>
                                        <option value='7'>7</option>
                                        <option value='8'>8</option>
                                        <option value='9'>9</option>
                                        <option value='10'>10</option>
                                    </select>
                                    @endif
                                </span>
                            @endif
                            <div class="alert alert-success" role="alert">{{ $explain[$i]['overview'] }}</div>
                        @endfor
                    @elseif ($movieData != null && $movieData[0] == "なし")
                        映画が見つかりませんでした。
                    @elseif ($error)
                        <div class="alert alert-danger" role="alert">
                            {{ $error }}
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
