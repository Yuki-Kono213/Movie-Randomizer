<!DOCTYPE html>
<html lang="ja">

<head>
    <!-- Required meta tags always come first -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title>movie Search</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css"
        integrity="sha384-y3tfxAZXuh4HwSYylfB+J125MxIs6mR5FOHamPBG064zB+AFeWH94NdvaCBm8qnd" crossorigin="anonymous">

    <style type="text/css">
        html {
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }

        body {

            background: none;

        }

        .container {

            text-align: center;
            margin-top: 100px;
            width: 450px;

        }

        input {

            margin: 20px 0;

        }

        #movie {

            margin-top: 15px;

        }

    </style>

</head>

<body>

    <div class="container">

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
            </fieldset>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <div id="movie"><?php

if ($movieArray) {
    foreach ($movieArray['results'] as $record) {
        $title = $record['title'];
        echo '<div class="alert alert-success" role="alert">' . $title . '</div>';
    }
} elseif ($error) {
    echo '<div class="alert alert-danger" role="alert">
  ' .
        $error .
        '
</div>';
}

?></div>
    </div>

    <!-- jQuery first, then Bootstrap JS. -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/js/bootstrap.min.js"
        integrity="sha384-vZ2WRJMwsjRMW/8U7i6PWi6AlO1L79snBrmgiDpgIWJ82z8eA5lenwvxbMV1PAh7" crossorigin="anonymous">
    </script>
</body>

</html>
