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

            <div class="content">

                <div id="movie">

                    @if ($movies != null)
                        @foreach ($movies as $movie)

                            <form method="post" id="input-form">
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
                                    @if (isset($movie->explain['rate']))
                                        <select name='rate'>
                                            <option value='0'>なし</option>
                                            <option value='1' <?= $movie->explain['rate'] == 1 ? 'selected' : '' ?>>1
                                            </option>
                                            <option value='2' <?= $movie->explain['rate'] == 2 ? 'selected' : '' ?>>2
                                            </option>
                                            <option value='3' <?= $movie->explain['rate'] == 3 ? 'selected' : '' ?>>3
                                            </option>
                                            <option value='4' <?= $movie->explain['rate'] == 4 ? 'selected' : '' ?>>4
                                            </option>
                                            <option value='5' <?= $movie->explain['rate'] == 5 ? 'selected' : '' ?>>5
                                            </option>
                                            <option value='6' <?= $movie->explain['rate'] == 6 ? 'selected' : '' ?>>6
                                            </option>
                                            <option value='7' <?= $movie->explain['rate'] == 7 ? 'selected' : '' ?>>7
                                            </option>
                                            <option value='8' <?= $movie->explain['rate'] == 8 ? 'selected' : '' ?>>8
                                            </option>
                                            <option value='9' <?= $movie->explain['rate'] == 9 ? 'selected' : '' ?>>9
                                            </option>
                                            <option value='10' <?= $movie->explain['rate'] == 10 ? 'selected' : '' ?>>
                                                10</option>
                                        </select>
                                        <input type="submit" class="btn btn-primary" name="btn-Renew" value="更新">

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
                                        <input type="submit" class="btn btn-primary" name="btn-Add" value="視聴リストに追加">

                                    @endif
                                </span>
                                <div class="alert alert-success" role="alert">{{ $movie->explain['overview'] }}</div>
                            </form>
                        @endforeach
                        {{ $movies->links() }}
                    @elseif ($movies != null && $movies[0] == "なし")
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
