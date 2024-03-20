@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
<div class="row">
  <div class="col-md-12">
    <ol class="breadcrumb">
      <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
      <li><a href="{{ route('reportes.index') }}">@trans('index.list_of') @trans('forms.reportes')</a></li>
      <li class="active">@trans('index.generar') @trans('index.reporte') @trans('reportes.' . $reporte->nombre)</li>
    </ol>
    <div class="page-header">
      <h3>
        @trans('index.generar') @trans('index.reporte') @trans('reportes.' . $reporte->nombre)
      </h3>
    </div>

			<div class="panel panel-default">
			  <div class="panel-body">
          <div class="alert alert-danger hidden"> <ul> </ul> </div>
			    <form class="form_reportes" method="POST" data-action="{{route('reportes.exportar', ['nombre' => $reporte->nombre])}}" id="form_reportes">
            {{ csrf_field() }}

            @foreach ($reporte->filtros as $keyFiltro => $valueFiltro)
              @if($valueFiltro->nombre == 'periodo')
                <div class="col-md-3 col-sm-6">
                  <div class="form-group">
                    <label for="desde">@trans('forms.desde')</label>
                    <input name="periodo[desde]" id="periodo_desde" class="form-control input-datepicker-m-y" type="text" placeholder="@trans('forms.desde')" required>
                  </div>
                </div>
                <div class="col-md-3 col-sm-6">
                  <div class="form-group">
                    <label for="hasta">@trans('forms.hasta')</label>
                    <input name="periodo[hasta]" id="periodo_hasta" class="form-control input-datepicker-m-y" type="text" placeholder="@trans('forms.hasta')" required>
                  </div>
                </div>
              @else
                <div class="col-md-6 col-sm-12 @if($valueFiltro->nombre == 'causante' && Auth::user()->usuario_causante) hidden @endif">
                  <div class="form-group form-group-chosen">
                    <label for="repre_tec_eby_id">{{$valueFiltro->nombre_trans}}</label>
                    <select class="form-control" name="{{$valueFiltro->nombre}}" id="{{$valueFiltro->nombre}}" data-placeholder="@trans('forms.multiple.' . $valueFiltro->nombre)"
                      {{-- multiple --}}
                      >
                      @foreach($valueFiltro->opciones as $key => $value )
                        <option value="{{$key}}" >{{$value}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              @endif
          @endforeach

            <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
              <div class="text-right">
                <a class="btn btn-small btn-default" id="limpiar">@trans('index.limpiar')</a>
                <a class="btn btn-small btn-success" href="{{ route('reportes.index') }}">@trans('forms.volver')</a>
                {{ Form::submit(trans('index.generar'), array('class' => 'btn btn-primary pull-right')) }}
              </div>
            </div>

			    </form>
			  </div>
			</div>
	  </div>
	</div>
@endsection

@section('scripts')
  $(document).ready( () => {
    $('#limpiar').on('click', function () {
      $("select").each(function( index ) {
        $(this).val("");
      });
      $("select").trigger("chosen:updated");

      $('.input-datepicker-m-y').val("");
    });
  });
@endsection
