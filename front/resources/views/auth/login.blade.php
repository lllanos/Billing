@extends('layout.app')

@section('title', config('app.name'))

@section('content')
  @include('auth.login_form')
@endsection
