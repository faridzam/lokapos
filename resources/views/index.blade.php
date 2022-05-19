<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>LokaPOS Desktop</title>

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('/js/scripts.js') }}"></script>
    <script src="{{ mix('/js/app.js') }}"></script>

    <!-- Datatables -->
    <script src="{{ asset('js/datatables.js') }}"></script>
    <link href="{{ asset('css/datatables.css')}}" rel="stylesheet" />

    <!-- swal -->
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <link href="{{ asset('css/sweetalert2.css')}}" rel="stylesheet" />

    <!-- virtual-keyboard -->
    <script src="{{ asset('js/jquery.keyboard.js') }}"></script>
    <script src="{{ asset('js/jquery.keyboard.min.js') }}"></script>
    <script src="{{ asset('js/jquery.mousewheel.min.js') }}"></script>
    <script src="{{ asset('js/jquery.keyboard.extension-all.min.js') }}"></script>
    <script src="{{ asset('js/jquery.keyboard.extension-altkeyspopup.min.js') }}"></script>
    <script src="{{ asset('js/jquery.keyboard.extension-autocomplete.min.js') }}"></script>
    <script src="{{ asset('js/jquery.keyboard.extension-caret.min.js') }}"></script>
    <script src="{{ asset('js/jquery.keyboard.extension-extender.min.js') }}"></script>
    <script src="{{ asset('js/jquery.keyboard.extension-mobile.min.js') }}"></script>
    <script src="{{ asset('js/jquery.keyboard.extension-navigation.min.js') }}"></script>
    <script src="{{ asset('js/jquery.keyboard.extension-previewkeyset.min.js') }}"></script>
    <script src="{{ asset('js/jquery.keyboard.extension-scramble.min.js') }}"></script>
    <script src="{{ asset('js/jquery.keyboard.extension-typing.min.js') }}"></script>
    <link href="{{ asset('css/keyboard.min.css')}}" rel="stylesheet"/>
    <link href="{{ asset('css/keyboard-basic.min.css')}}" rel="stylesheet"/>
    <link href="{{ asset('css/keyboard-dark.min.css')}}" rel="stylesheet"/>
    <link href="{{ asset('css/keyboard-previewkeyset.min.css')}}" rel="stylesheet"/>


    <style>
        .navbar-right{
            display: flex;
            align-items: center;
        }

        .container{
            display: flex;
        }

        .ui-keyboard{
        }
        .ui-keyboard-keyset{
            top: 50%;
        }
        .ui-keyboard-button{
            width: 6rem;
            height: 4rem;
        }
        .ui-keyboard-space{
            width: 35rem;
            height: 4rem;
        }
        .ui-keyboard-bksp{
            width: 15rem;
            height: 4rem;
        }
        .ui-keyboard-button span{
            font-size: 2em;
        }
        .ui-keyboard-actionkey{
            font-size: 2.5em;
        }
    </style>
    @yield('styles')

    <link href="{{ asset('css/styles.css')}}" rel="stylesheet" />

  </head>
  <body>

    <div id="app">
        <example-component></example-component>
    </div>

    @yield('scripts')

  </body>
</html>
