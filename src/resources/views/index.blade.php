<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        .index-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        h1, h3, h5 {
            text-align: center;
        }

        a {
            text-align: center;
            display: inherit;
            height: 40px
        }

        .button {
            background-color: #E50000;
            color: white;
            border-style: solid;
            border-color: white;
            border-radius: 10px;
            height: 40px
        }

        .button:hover {
            background-color: #BD0000;
        }

        .button-inner-wrap {
            width: 200px;
            margin: auto;
        }

        .button span {
            cursor: pointer;
            display: inline-block;
            position: relative;
            transition: 0.5s;
        }

        .button span:after {
            content: '\2192';
            position: absolute;
            opacity: 0;
            top: 0;
            right: -20px;
            transition: 0.5s;
        }

        .button:hover span {
            padding-right: 25px;
        }

        .button:hover span:after {
            opacity: 1;
            right: 0;
        }
    </style>
</head>
<body>
<div class="main-container">
    <div class="index-container">
        <h1>Mobile Money Operator Simulator</h1>
        <h3>Welcome to the index page</h3>
        <h5>If you would like to use the test platform, please use the button below to be redirected to the main page</h5>
        <div class="button-outer-wrap">
            <div class="button-inner-wrap">
                <a href="http://interop.gsmainclusivetechlab.io" target="_blank"><button class="button"><span>Take me to the main page</span></button></a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
