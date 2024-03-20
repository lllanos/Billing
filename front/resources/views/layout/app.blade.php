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
  <body>
    <div id="app">
      <div class="gralState"></div>

      @include('layout.nav')

      <div class="container">
        @yield('content')
      </div>

      @include('layout.toast')

      @yield('modals')

      @include('layout.footer')

    </div>
    @include('layout.modal.index')

    <script type="text/javascript" src="{{asset('js/app.js')}}"></script>

    <script type="text/javascript">
      @yield('scripts')
      $('#modal_ayuda').on('shown.bs.modal', function () {
        $('#modal_ayuda .modalContentScrollable').animate({scrollTop: 0}, 100);
        var scrollTarget = $.trim($('#scroll_ayuda').val());
        if(scrollTarget != ''){
          var target = $('#'+scrollTarget);

            setTimeout(function(){
              $('#modal_ayuda .modalContentScrollable').animate({scrollTop: (target.offset().top) - 100}, 500);
            }, 300);

        }
      });
    </script>
  </body>
</html>
