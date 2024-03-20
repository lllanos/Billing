@extends ('layout.app')

@section('title', config('app.name'))

@section('content')
<div class="row">
  <div class="col-md-12">
    <ol class="breadcrumb">
      <li><a href="{{url('/')}}">Tests</a></li>
      <li class="active">Simulador Variación Polinómica</li>
    </ol>
    <div class="page-header">
      <h3>
        Simulador Variación Polinómica
      </h3>
    </div>
      @if (count($errors) > 0)
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
            	<li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

			<div class="panel panel-default">
			  <div class="panel-body">
          <form method="POST" action="{{ route('test.simuladorVariacionPolinomica.post') }}">
            {{ csrf_field() }}

            <div class="col-md-12 col-sm-6">
              <div class="form-group">
                <label for="contrato_id">@trans('index.contrato')</label>
                <select class="form-control select-html-change" name="contrato_id" id="contrato_id"
                  data-action="{{route('test.obrasFromContrato.html', ['id' => ':id'])}}" required>
                  <option disabled selected value> {{trans('forms.select.contrato')}}</option>
                  @foreach($contratos_select as $opcion)
                    <option value="{{$opcion['id']}}" >{{$opcion['value']}} </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-md-12 col-sm-6">
              <div class="form-group">
                <label for="obra_id">{{trans('index.obra')}}</label>
                <select class="form-control select-html-change" name="obra_id" id="obra_id"
                  data-action="{{route('test.VariacionIndicePolinomicaFromObra.html', ['id' => ':id'])}}" required>
                  <option disabled selected value> {{trans('forms.select.obra')}}</option>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12 col-sm-12">
                <div class="form-group">
                  {{ Form::label('fecha_ultima_redeterminacion', 'Fecha Última Redeterminación') }}
                  {{ Form::text('fecha_ultima_redeterminacion', '', array('class' => 'form-control', 'required', 'placeholder' => 'Fecha Última Redeterminación', 'readonly')) }}
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4 col-sm-12">
                <div class="form-group">
                  {{ Form::label('mes_anterior_variacion', 'Mes de última variación') }}
                  {{ Form::text('mes_anterior_variacion', '', array('class' => 'form-control', 'required', 'placeholder' => 'Mes de última variación', 'readonly')) }}
                </div>
              </div>

              <div class="col-md-4 col-sm-12">
                <div class="form-group">
                  {{ Form::label('anio_anterior_variacion', 'Año de última variación') }}
                  {{ Form::text('anio_anterior_variacion', '', array('class' => 'form-control', 'required', 'placeholder' => 'Año de última variación', 'readonly')) }}
                </div>
              </div>

              <div class="col-md-4 col-sm-12">
                <div class="form-group">
                  {{ Form::label('variacion_anterior', 'Índice de última variación') }}
                  {{ Form::text('variacion_anterior', '', array('class' => 'form-control', 'required', 'placeholder' => 'Índice de última variación', 'readonly')) }}
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4 col-sm-12">
                <div class="form-group">
                  {{ Form::label('mes_siguiente_variacion', 'Mes de nueva variación') }}
                  {{ Form::text('mes_siguiente_variacion', '', array('class' => 'form-control', 'required', 'placeholder' => 'Mes de nueva variación')) }}
                </div>
              </div>

              <div class="col-md-4 col-sm-12">
                <div class="form-group">
                  {{ Form::label('anio_siguiente_variacion', 'Año de nueva variación') }}
                  {{ Form::text('anio_siguiente_variacion', '', array('class' => 'form-control', 'required', 'placeholder' => 'Año de nueva variación')) }}
                </div>
              </div>

              <div class="col-md-4 col-sm-12">
                <div class="form-group">
                  {{ Form::label('variacion_siguiente', 'Índice de nueva variación') }}
                  {{ Form::text('variacion_siguiente', '', array('class' => 'form-control', 'required', 'placeholder' => 'Índice de nueva variación')) }}
                </div>
              </div>
            </div>


            <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
              <div class="text-right">
                <a class="btn btn-small btn-success" href="{{ url('seguridad/usuarios') }}">@trans('forms.volver')</a>
                {{ Form::submit(trans('forms.guardar'), array('class' => 'btn btn-primary pull-right')) }}
              </div>
            </div>

			    </form>
			  </div>
			</div>
	  </div>
	</div>
@endsection

@section('scripts')
  applyHtmlChange();
@endsection
