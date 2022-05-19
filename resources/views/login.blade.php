<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.88.1">
    <title>PoSALOKA Login</title>

    <script src="{{ asset('js/app.js') }}"></script>

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

        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif
}
body {
    background: #ecf0f3;
    overflow:hidden;
}

.btn{
    background-color: #169870 !important;
}
.ui-keyboard-input-current{
    box-shadow: none;
}
.wrapper {
    margin-left: auto;
    margin-right: 0;
    max-width: 600px;
    min-height: 500px;
    height: 100vh;
    padding: 40px 30px 30px 30px;
    background-color: #ecf0f3;
    border-radius: 15px;
    box-shadow: 0px 0px 40px #556052, -0px -0px 0px #556052
}

.logo {
    display: flex;
    width: 100px;
    margin: auto;
    margin-top: 15rem;
}

.logo img {
    width: 100%;
    height: 100px;
    object-fit: contain;
    border-radius: 50%;
    box-shadow: 0px 0px 3px #5f5f5f, 0px 0px 0px 5px #ecf0f3, 8px 8px 15px #a7aaa7, -8px -8px 15px #fff
}
.wrapper .name {
    font-weight: 600;
    font-size: 1.4rem;
    letter-spacing: 1.3px;
    padding-left: 10px;
    color: #555
}

.wrapper .form-field input {
    width: 100%;
    display: block;
    border: none;
    outline: none;
    background: none;
    font-size: 1.2rem;
    color: #666;
    padding: 10px 15px 10px 10px
}

.wrapper .form-field {
    padding-left: 10px;
    margin-bottom: 20px;
    border-radius: 20px;
    box-shadow: inset 8px 8px 8px #cbced1, inset -8px -8px 8px #fff
}

.wrapper .form-field .fas {
    color: #555
}

.wrapper .btn {
    box-shadow: none;
    width: 100%;
    height: 40px;
    background-color: #03A9F4;
    color: #fff;
    border-radius: 25px;
    box-shadow: 3px 3px 3px #b1b1b1, -3px -3px 3px #fff;
    letter-spacing: 1.3px
}

.wrapper .btn:hover {
    background-color: #039BE5
}

.wrapper a {
    text-decoration: none;
    font-size: 0.8rem;
    color: #03A9F4
}

.wrapper a:hover {
    color: #039BE5
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

@media(max-width: 380px) {
    .wrapper {
        margin: 30px 20px;
        padding: 40px 15px 15px 15px
    }
}

</style>
  </head>
  <body class="text-center">

    <main class="form-signin">
    <img style="position:absolute;z-index:-1;height: 10%;width: 10%;object-fit: fill; margin-left: 32.5%; margin-right: auto;" src="../img/logo_login.svg" alt="saloka logo">
    <img style="position:absolute;z-index:-2;height: 100%;width: 100%;object-fit: fill;" src="../img/banner-merchandise-croped.jpg" alt="saloka logo">

    {{-- handler login error/success --}}

    @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="position:absolute; background-color: red; width: 100%: height:10%">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
    </div>
    @endif

    {{-- Login Form --}}

    <div class="wrapper">
        <div class="logo"> <img src="../img/logoSalokaSS.png" alt="saloka logo"> </div>
        <div class="text-center mt-3 name" style="text-align: center; margin-top: 1rem;">LOKAPOS</div>
        <form class="p-3 mt-3" style="margin-top: 1rem" action="/login" method="POST">
            @csrf
            <div class="form-field d-flex align-items-center mt-2" style="height:3.5rem;width: 70%;margin-left:auto;margin-right:auto;"> <span class="far fa-user"></span> <input style="border-radius: 20px;font-size:25px;" autocomplete="off" type="text" name="username" id="username" placeholder="Username"> </div>
            <div class="form-field d-flex align-items-center mt-2" style="height:3.5rem;width: 70%;margin-left:auto;margin-right:auto;"> <span class="fas fa-key"></span> <input style="border-radius: 20px;font-size:25px;" type="password" name="password" id="password" placeholder="Password"> </div>
            <div style="width:30%;margin-left:auto;margin-right:auto;"><button class="btn btn-lg mt-4 align-items-center" style="border-style: none;" type="submit">Login</button></div>
        </form>
        <div class="text-center mt-3 name" style="display: flex;justify-content: space-between;text-align: left;font-size:12px;opacity: 0.5;padding-top:38vh;">
            <div class="footer-left">
            Copyright &copy; 2022 <div class="bullet"></div> Design & Developed By IT SALOKA
            </div>
            <div class="footer-right">
            version 2.0
            </div>
        </div>
    </div>

    </main>

    </body>

    <script>
        $(function(){
            $('#username').keyboard({
                layout: 'custom',
                customLayout: {
                    'normal': [
                        '1 2 3 4 5 6 7 8 9 0 {bksp}',
                        'q w e r t y u i o p [ ] \ ',
                        'a s d f g h j k l ; "',
                        'z x c v b n m , . /',
                        '{space}'],
                },
                // true: preview added above keyboard;
                // false: original input/textarea used
                usePreview: false,

                // Auto-accept content when clicking outside the
                // keyboard (popup will close)
                autoAccept: true,
                closeByClickEvent:true,
            });
        });
    </script>

    <script>
        $(function(){
            $('#password').keyboard({
                layout: 'custom',
                customLayout: {
                    'normal': [
                        '1 2 3 4 5 6 7 8 9 0 {bksp}',
                        'q w e r t y u i o p [ ] \ ',
                        'a s d f g h j k l ; "',
                        'z x c v b n m , . /',
                        '{space}'],
                },
                // true: preview added above keyboard;
                // false: original input/textarea used
                usePreview: false,

                // Auto-accept content when clicking outside the
                // keyboard (popup will close)
                autoAccept: true,
                closeByClickEvent:true,
            });
        });
    </script>

    </html>
