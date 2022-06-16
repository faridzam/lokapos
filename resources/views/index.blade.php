<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="this application is made by faridzam.com">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>LokaPOS Desktop</title>

    <style>

        *{
            user-select: none;
        }
        .cursor-pointer{
            cursor: pointer;
        }

        .row{
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            grid-gap: 10px;
        }

        .col-12{
            grid-column: span 12;
        }
        .col-11{
            grid-column: span 11;
        }
        .col-10{
            grid-column: span 10;
        }
        .col-9{
            grid-column: span 9;
        }
        .col-8{
            grid-column: span 8;
        }
        .col-7{
            grid-column: span 7;
        }
        .col-6{
            grid-column: span 6;
        }
        .col-5{
            grid-column: span 5;
        }
        .col-4{
            grid-column: span 4;
        }
        .col-3{
            grid-column: span 3;
        }
        .col-2{
            grid-column: span 2;
        }
        .col-1{
            grid-column: span 1;
        }
/*
        .ui-keyboard{
        }
        .ui-keyboard-keyset{
            top: 50%;
        }
        .ui-keyboard-button{
            width: 6rem !important;
            height: 4rem !important;
        }
        .ui-keyboard-space{
            width: 35rem !important;
            height: 4rem !important;
        }
        .ui-keyboard-bksp{
            width: 15rem !important;
            height: 4rem !important;
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
        } */

    </style>
    @yield('styles')

  </head>
  <body>

    <div id="app" style="display: flex; justify-content: center;flex-wrap: wrap;">
        <navbar-component></navbar-component>
        <app-component></app-component>
    </div>

    @yield('scripts')

    <!-- Main -->
    {{-- <script src="{{ mix('/js/app.js') }}"></script> --}}
    <script src="{{ asset('js/app.js') }}"></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('/js/scripts.js') }}"></script>

    <!-- Datatables -->
    <script src="{{ asset('js/datatables.js') }}"></script>
    <link href="{{ asset('css/datatables.css')}}" rel="stylesheet" />

    <!-- swal -->
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <link href="{{ asset('css/sweetalert2.css')}}" rel="stylesheet" />

    <script>

    </script>

  </body>


</html>
