<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link rel="shortcut icon" href="{{asset('favicon.ico')}}" type="image/x-icon">
    <link rel="icon" href="{{asset('favicon.ico')}}" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="{{asset('css/app.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/all.css')}}">

    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
  </head>

  <body id="page-login">
    <div class="gralState"></div>

    @include('layout_login.nav')

    <div class="row">
      <div class="content-login">
        <div class="col-md-12 m-0">
          <div class="panel panel-default panel-login">
            <div class="panel-heading heading-login">
              <h2 class="panel-title title-login">
                <img src="{{asset ('img/main-logo-eby-arg.png')}}" class="img-login">
              </h2>
            </div>
            @yield('content')
          </div>
        </div>
      </div>
    </div>

    <script type="text/javascript" src="{{asset('js/app.js')}}"></script>
  </body>
</html>
