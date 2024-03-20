@extends('layout_login.app')

@section('title', config('app.name'))

@section('content')

   <div class="titleLogin text-center">
     <h3>{!! trans('login.recuperar_contrasenia') !!}</h3>
   </div>
   <div class="panel-body">
     <form class="form-horizontal" role="form" method="POST" action="{{ route('password.email') }}" id="formLogin">
       {{ csrf_field() }}
       @if (Session::get('status'))
         <div class="alert alert-success">
           {{ Session::get('status') }}
         </div>
        @endif
       <div class="col-md-10 col-md-offset-1">
         <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
           <label for="email">{!! trans('login.introduce_email') !!}</label>
           <input id="email" type="text" class="form-control bg-bright enter_submit" name="email" value="{{ old('email') }}" required placeholder="{!! trans('login.introduce_email') !!}" autofocus>
             @if ($errors->has('email'))
               <span class="help-block" id="error_email">
                 <strong>{{ $errors->first('email') }}</strong>
               </span>
             @endif
         </div>
       </div>

         <div class="col-md-10 col-md-offset-1">
           <div class="form-group formGroupZeroMargin">
             <div class="row">
               <div class="col-md-12">
                 <button type="submit" class="btn btn-primary btn-block" id="btn_login">
                    @trans('login.pedir_contrasenia')
                 </button>
               </div>
             </div>
           </div>
         </div>
       </form>
     </div>
@endsection
