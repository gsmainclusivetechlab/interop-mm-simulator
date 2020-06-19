<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ mix('css/app.css', 'assets') }}" rel="stylesheet">
</head>
<body>
<div class="main-container">
    <div class="index-container">
        <p class="h1">Mobile Money Operator Simulator</p>
        <p class="h3">Welcome to the Simulator</p>
        <p class="text-muted">If you would like to use the test platform, please use the button below to be redirected to the main page</p>
        <div class="button-outer-wrap">
            <div class="button-inner-wrap">
                <a href="https://interop.gsmainclusivetechlab.io" target="_blank"><button class="button"><span>Take me to the main page</span></button></a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
