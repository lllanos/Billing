@extends('layout.app')

@section('title', config('app.name'))

@section('content')
  <div class="container">
    <div class="row">
      <div class="fullMensaje">
        <h3>{{$mensaje}}</h3>
      </div>
    </div>
  </div>
@endsection
