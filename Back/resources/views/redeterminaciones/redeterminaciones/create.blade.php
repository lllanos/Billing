@extends ('layout.app')

@section('title', config('app.name'))

@section('content')
<div class="row">
  <div class="col-md-12">
    <ol class="breadcrumb">
      <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
      <li><a href="{{route('contratos.index')}}">@trans('index.contratos')</a></li>
      <li><a href="{{route('contratos.ver', ['id' => $contrato->id]) }}">@trans('forms.contrato') {{$contrato->expediente_madre}}</a></li>
      <li class="active"> @trans('index.nueva') @trans('index.redeterminacion')</li>
    </ol>
    <div class="page-header">
      <h3 class="page_header__titulo">
        <div class="titulo__contenido">
          @trans('index.nueva') @trans('index.redeterminacion') @trans('index.de')
          {{$contrato->expediente_madre}}
        </div>
      </h3>
    </div>

		<div class="panel panel-default">
      <div class="panel-body">
        <div class="row">
          <form role="form" method="POST" data-action="{{route('empalme.createRedeterminacion.store', ['contreato_id' => $contrato->id])}}" id="form-ajax">
            {{ csrf_field() }}

            <div class="alert alert-danger hidden"> <ul> </ul> </div>
            <div class="col-md-4 col-sm-6">
            	<div class="form-group">
            		<label for="publicacion_id">{{trans('forms.moneda')}}</label>
            		<select class="form-control" name="contrato_moneda_id" id="contrato_moneda_id" required>
                  <option disabled selected value> {{ trans('forms.select.moneda') }}</option>
                  @foreach($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
                    @if(!$valueContratoMoneda->has_redeterminaciones_empalme_borrador)
          			     <option value="{{$valueContratoMoneda->id}}">{{$valueContratoMoneda->moneda->nombre_simbolo}}</option>
                   @endif
                  @endforeach
            		</select>
            	</div>
            </div>

            <div class="col-md-4 col-sm-6">
            	<div class="form-group">
            		<label for="publicacion_id">@trans('index.publicacion')</label>
            		<select class="form-control" name="publicacion_id" id="publicacion_id" required>
                  @foreach($publicaciones as $keyPublicacion => $valuePublicacion )
              			<option value="{{$keyPublicacion}}">{{$valuePublicacion}}</option>
                  @endforeach
            		</select>
            	</div>
            </div>

            <div class="col-md-4 col-sm-6">
              <div class="form-group">
                <label>@trans('redeterminaciones.nro_salto')</label>
                <input type="number" min="1" step="1" id="nro_salto" name="nro_salto" class="form-control" placeholder="@trans('redeterminaciones.nro_salto')" required>
              </div>
            </div>

            <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
              <div class="text-right">
                <a class="btn btn-small btn-success" href="{{route('contratos.ver.incompleto', ['id' => $contrato->id, 'accion' => 'empalme'])}}">@trans('forms.volver')</a>
                {{ Form::submit(trans('forms.guardar'), array('class' => 'btn btn-primary pull-right')) }}
              </div>
            </div>
          </form>
        </div>
      </div>
		</div>

  </div>
</div>
@endsection
