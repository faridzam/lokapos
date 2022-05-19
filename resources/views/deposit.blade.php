<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="{{ asset('js/app.js') }}" async defer></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        body{
            font-family: Roboto;
            background-attachment: fixed;
            background: linear-gradient(0deg, rgba(22,152,112,1) 0%, rgba(56,193,114,1) 100%);
            background-repeat: no-repeat;
            padding: 0;
            margin: 0;
            width: 100vw;
            height: 100vh;
        }
        input{
            background: none;
        }
        .flex-break{
            flex-basis: 100%;
            height: 0;
        }
    </style>

    <title>Deposit</title>
</head>
<body>
    <div id="app" style="display: flex; justify-content: center;flex-wrap: wrap;">
        <deposit-component></deposit-component>
    </div>
</body>
</html>
