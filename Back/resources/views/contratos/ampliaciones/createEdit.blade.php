@extends ('layout.app')

@section('title', config('app.name'))

@section('content')
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li><a href="{{route('contratos.index')}}">@trans('index.contratos')</a></li>
        <li><a href="{{route('contratos.ver', ['id' => $ampliacion->contrato->id]) }}">@trans('forms.contrato') {{$ampliacion->contrato->expediente_madre}}</a></li>
        <li class="active">
          @if($ampliacion->id == null) @trans('index.solicitar') @else @trans('index.editar') @endif @trans('contratos.ampliacion')
        </li>
      </ol>
      <div class="page-header">
        <h3 class="page_header__titulo">
          <div class="titulo__contenido">
            @if($ampliacion->id == null)
             @trans('index.solicitar') @trans('contratos.ampliacion') @trans('index.de')
            @else
              @trans('index.editar')
              @trans('contratos.tipo_ampliacion.' . $tipo_ampliacion) @trans('index.de')
            @endif
            {{$ampliacion->contrato->expediente_madre}}
          </div>
          <div class="buttons-on-title">
            @if($ampliacion->id != null)
              @permissions(($ampliacion->tipo_ampliacion->nombre . '-view'))
                @if($ampliacion->id != null)
                  <div class="button_desktop">
                    <a class="btn btn-success pull-right" href="{{route('ampliacion.ver', ['id' => $ampliacion->id]) }}">
                      @trans('index.ver') @trans('contratos.tipo_ampliacion.' . $ampliacion->tipo_ampliacion->nombre)
                    </a>
                  </div>
                  <div class="button_responsive">
                    <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
                      <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                        <i class="fa fa-ellipsis-v"></i>
                      </button>
                      <ul class="dropdown-menu pull-right">
                        <li>
                          <a href="{{route('ampliacion.ver', ['id' => $ampliacion->id]) }}">
                            @trans('index.ver') @trans('contratos.tipo_ampliacion.' . $ampliacion->tipo_ampliacion->nombre)
                          </a>
                        </li>
                      </ul>
                    </div>
                  </div>
                @endif
              @endpermission
            @endif
          </div>
        </h3>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-body">
          <form role="form" method="POST" data-action="{{route('ampliacion.storeUpdate')}}" id="form-ajax">
            {{ csrf_field() }}
            <input type='text' id='borrador' name='borrador' class='hidden' value="0">
            <input type='text' id='id' name='id' class='hidden' value="{{$ampliacion->id}}">
            <input type='text' id='contrato_id' name='contrato_id' class='hidden' value="{{$ampliacion->contrato_id}}">

            <div class="alert alert-danger hidden">
              <ul> </ul>
            </div>

            @if($ampliacion->id == null)
              <div class="col-md-12">
              	<div class="form-group select-create">
              		<label for="tipo_ampliacion">{{trans('index.tipo_ampliacion')}}</label>
              		<select class="form-control" name="tipo_id" id="tipo_id" required
                          data-action="{{route('ampliacion.getViews', ['contrato_id' => $contrato_id, 'tipo_id' => ':tipo_id'])}}" >
                    <option disabled selected value> @trans('forms.select.tipo_ampliacion')</option>
                    @foreach($select_options as $key => $value)
                			<option value="{{$key}}">@trans('contratos.tipo_ampliacion.' . $value)</option>
                    @endforeach
              		</select>
              	</div>
              </div>
            @endif

            <div id="tipo_ampliacion_div">
              @if($ampliacion->id != null)
                @include('contratos.ampliaciones.forms.index')
              @endif
            </div>

            <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
              <div class="text-right">
                <a class="btn btn-small btn-success" href="{{ route('contratos.ver', ['id' => $ampliacion->contrato->id]) }}">@trans('forms.volver')</a>
                {{ Form::submit(trans('forms.guardar'), array('class' => 'btn btn-primary pull-right btn_guardar')) }}
                {{ Form::submit(trans('forms.guardar_borrador'), array('class' => 'btn btn-basic pull-right borrador')) }}
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  $(document).ready(() => {
    $('#tipo_id').unbind('change').on('change', function() {
      var action = $(this).data('action');
      action = action.replace(':tipo_id', $(this).find(":selected").val());
      $.get(action, function(resp) {
        if(resp.status == true)
          $('#tipo_ampliacion_div').html(resp.view);
        else
          window.location.href = resp.url;
        applyAllAmpliacion();
      });
    });

    applyAllAmpliacion();
  });

  applyAllAmpliacion = () => {
    applyAll();
  };
@endsection
