@extends ('layout.app')

@section('title', config('app.name'))

@section('title-nav', trans('index.dashboard'))
@section('content')
<div class="row">
	<div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li class="active">{!!trans('index.normativas')!!}</li>
      </ol>
      <div class="page-header">
        <h3 class="page_header__titulo">
          <div class="titulo__contenido">
            {!!trans('index.normativas')!!}
          </div>
        </h3>
      </div>
    </div>
	<div class="col-md-12 mb-2">
		<h3 class="mb-1">{{trans('index.decreto_nro')}}<a href="http://servicios.infoleg.gob.ar/infolegInternet/verNorma.do;jsessionid=73E6981696AA4DA774BD3EBABCA85322?id=76101" target="_blank">{{trans('index.1295_02')}}</a></h3>
		<span>{{trans('index.decreto_1295_2002')}}</span><br>
		<span>{{trans('index.obras_publicas')}}</span><br>
		<span>{{trans('index.redeterminacion_precios_contratos')}}</span><br>
		<span>{{trans('index.resumen')}}:</span><br>
		<span>{{trans('index.resumen_decreto_1295_2002')}}</span>
	</div>
	<div class="col-md-12">
		<h3 class="mb-1">{{trans('index.decreto_nro')}}<a href="http://servicios.infoleg.gob.ar/infolegInternet/verNorma.do?id=261512" target="_blank">{{trans('index.691_16')}}</a></h3>
		<span>{{trans('index.decreto_691_16')}}</span><br>
		<span>{{trans('index.admin_pub_nacional')}}</span><br>
		<span>{{trans('index.regimen_rdp')}}</span><br>
		<span>{{trans('index.resumen')}}:</span><br>
		<span>{{trans('index.resumen_decreto_691_162')}}</span>
	</div>
	<div class="col-md-12">
		<h3 class="mb-1">{{trans('index.informacion_adicional')}}</h3>
		<a href="{{asset('descargas/ConsultasD691urp.zip')}}">{{trans('index.descarga_adhesiones')}}</a><br>
		<a href="{{asset('descargas/Redeterminacion.zip')}}">{{trans('index.descarga_redeterminaciones')}}</a><br>
		<a href="{{asset('descargas/VarErmYActualizaciones.zip')}}">{{trans('index.descarga_var_erm')}}</a>
	</div>
</div>
@endsection
