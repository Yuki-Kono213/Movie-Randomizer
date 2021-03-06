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
        <div class="movie-article">

            <div class="movie-content">

                <div id="movie">
                    @if ($movies != null)
                        <a href={{route('sort_rate')}}>評価順で並び替え</a>
                        <a href={{route('sort_updated')}}>更新順で並び替え</a>
                        @foreach ($movies as $movie)

                            <form method="post" id="input-form">
                                @csrf
                                @if($movies->sort_order != null)
                                    <input type="hidden" name="order" value={{$movies->sort_order}}>
                                @endif
                                <?php
                                echo $movie->explain['imgtxt'];
                                ?>

                                <div class="alert alert-success" role="alert">{{ $movie->explain['title'] }}</div>
                                <span class="alert alert-success" role="alert">平均評価{{ $movie->explain['vote_average'] }}</span>
                                <span class="alert alert-success" role="alert">上映年{{ $movie->explain['release_date'] }}</span>
                                <div class="alert alert-success" role="alert">上映時間{{ $movie->explain['runtime'] }}分
                                </div>
                                <span class="alert alert-success" role="alert">評価数{{ $movie->explain['vote_count'] }}</span>
                                <input type="hidden" name="movie_id" value="{{ $movie->explain['id'] }}">
                                <span class="alert alert-success" role="alert">あなたの評価
                                    @if (isset($movie->movie_rate))
                                        <select name='rate'>
                                            <option value='0'>なし</option>
                                            <option value='1' <?= $movie->movie_rate == 1 ? 'selected' : '' ?>>1
                                            </option>
                                            <option value='2' <?= $movie->movie_rate == 2 ? 'selected' : '' ?>>2
                                            </option>
                                            <option value='3' <?= $movie->movie_rate == 3 ? 'selected' : '' ?>>3
                                            </option>
                                            <option value='4' <?= $movie->movie_rate == 4 ? 'selected' : '' ?>>4
                                            </option>
                                            <option value='5' <?= $movie->movie_rate == 5 ? 'selected' : '' ?>>5
                                            </option>
                                            <option value='6' <?= $movie->movie_rate == 6 ? 'selected' : '' ?>>6
                                            </option>
                                            <option value='7' <?= $movie->movie_rate == 7 ? 'selected' : '' ?>>7
                                            </option>
                                            <option value='8' <?= $movie->movie_rate == 8 ? 'selected' : '' ?>>8
                                            </option>
                                            <option value='9' <?= $movie->movie_rate == 9 ? 'selected' : '' ?>>9
                                            </option>
                                            <option value='10' <?= $movie->movie_rate == 10 ? 'selected' : '' ?>>
                                                10</option>
                                        </select>
                                        <input type="submit" class="btn btn-primary" name="btn-Renew" value="更新">
                                        <input type="submit" class="btn btn-primary" name="btn-Delete" value="削除">

                                    @else
                                        <select name='rate'>
                                            <option value='0' selected="true">なし</option>
                                            <option value='1'>1</option>
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
                                        <input type="submit" class="btn btn-primary" name="btn-Renew" value="評価">

                                    @endif
                                </span>
                                <div class="alert alert-success" role="alert">{{ $movie->explain['overview'] }}</div>
                            </form>
                        @endforeach
                    @elseif ($movies != null && $movies[0] == "なし")
                        映画が見つかりませんでした。
                    @elseif ($error)
                        <div class="alert alert-danger" role="alert">
                            {{ $error }}
                        </div>;
                    @endif

                    {{ $movies->links('vendor.pagination.sample-pagination') }}
                    <a href={{route('Index')}}>戻る</a>
                </div>
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
