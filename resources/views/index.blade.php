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
                <h1>Random映画</h1>



                <form method="get" id="input-form">
                    @csrf
                    <fieldset class="form-group">
                        <div>
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
                                                <option value="{{ $key }}" selected> {{ $pref }}
                                                </option>;
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
                            <input type="text" class="form-control" name="minimum_age" id="minimum_age"
                                placeholder="最小上映年" value={{ $config['minimum_age'] }}>
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
                    <input type="submit" class="btn btn-primary" name="btn-Random" value="Submit">
                    <div>
                        @if ($user == null)
                            <a href="{{ route('login') }}"
                                class="text-sm text-gray-700 dark:text-gray-500 underline">ログイン</a>
                        @else
                            <a href="{{ route('loggedOutRoute') }}"
                                class="text-sm text-gray-700 dark:text-gray-500 underline">ログアウト</a>
                            <a href="{{ route('sort_updated') }}" type="submit" class="btn btn-secondary"
                                name="btn-MyMovie"
                                class="text-sm text-gray-700 dark:text-gray-500 underline">評価した映画を表示</a>
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
                <form method="post" id="input-form">


                    <div id="movie">

                        @if ($movies != null)
                            @foreach ($movies as $movie)

                                @csrf
                                <?php
                                echo $movie['explain']['imgtxt'];
                                ?>

                                <div class="alert alert-success" role="alert">{{ $movie['explain']['title'] }}</div>
                                <span class="alert alert-success"
                                    role="alert">平均評価{{ $movie['explain']['vote_average'] }}</span>
                                <span class="alert alert-success"
                                    role="alert">上映年{{ $movie['explain']['release_date'] }}</span>
                                <div class="alert alert-success" role="alert">上映時間{{ $movie['explain']['runtime'] }}分
                                </div>
                                <span class="alert alert-success"
                                    role="alert">評価数{{ $movie['explain']['vote_count'] }}</span>
                                <input type="hidden" name="movie_id" value="{{ $movie['explain']['id'] }}">
                                @if ($user != null)
                                    @if (isset($movie['explain']['rate']))
                                        <span class="alert alert-success" role="alert">あなたの評価
                                            {{ $movie['explain']['rate'] }}
                                        @else
                                            <label>
                                                <input type="checkbox" class="cb" name="category[]"
                                                    value="{{ $movie['explain']['id'] }}">
                                                <span> 視聴する</span></label>
                                    @endif
                                    </span>
                                @endif
                                <div class="alert alert-success" role="alert">{{ $movie['explain']['overview'] }}</div>

                            @endforeach
                        @elseif ($movies == null)
                            映画が見つかりませんでした。
                        @elseif ($error)
                            <div class="alert alert-danger" role="alert">
                                {{ $error }}
                            </div>;
                        @endif
                        @if ($movies != null)
                            <input type="submit" class="btn btn-primary" name="btn-Add" value="チェックした映画を視聴リストに追加">
                        @endif

                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery first, then Bootstrap JS. -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/js/bootstrap.min.js"
        integrity="sha384-vZ2WRJMwsjRMW/8U7i6PWi6AlO1L79snBrmgiDpgIWJ82z8eA5lenwvxbMV1PAh7" crossorigin="anonymous">
    </script>
</body>

</html>
