@extends ('layout.app')

@section('title', config('app.name'))

@section('content')
<div class="row">
  <div class="col-md-12">
    <ol class="breadcrumb">
      <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
      <li><a href="{{route('alarmas.solicitud')}}">@trans('index.alarmas')</a></li>
      <li class="active">@trans('index.crear') @trans('forms.alarma_solicitud')</li>
    </ol>
    <div class="page-header">
      <h3>@trans('index.crear') @trans('forms.alarma_solicitud')</h3>
    </div>
    <!--Panel-->
    <div class="panel panel-default">
      <div class="panel-body">
        <form method="POST" data-action="{{ route('alarmas.solicitud.update') }}" id="form-ajax">
          {{ csrf_field() }}

          <div class="row">
            <div class="col-md-6 col-sm-12">
              <div class="form-group">
                {{ Form::label('nombre', trans('forms.nombre')) }}
                {{ Form::text('nombre', '', array('class' => 'form-control', 'required', 'autofocus', 'placeholder' => trans('forms.nombre'))) }}
              </div>
            </div>
          </div>

          <div class="page-header">
            <h3 class="m-0" >@trans('forms.receptor')</h3>
          </div>
          <div class="row">
            <div class="col-md-12 col-sm-12">
              <div class="form-group">
                <label class="col-md-3">
                  <input type="radio" name="usuario_sistema" class="toggleSelect" value="0" checked>@trans('forms.contratista')
                </label>
                <label class="col-md-3">
                  <input type="radio" name="usuario_sistema" class="toggleSelect" value="1">@trans('index.eby')
                </label>
              </div>
            </div>
            <!-- Contratista -->
            <div class="col-md-12 col-sm-12 toggleHidden">
              Usuario del Contrato
            </div>
            <!--Fin contratista-->
            <!-- DNV -->
            <div class="col-md-12 col-sm-12 toggleHidden hidden container_up_chosen">
              <div class="col-md-6 col-sm-12">
                <div class="form-group">
                  <label for="role_id">@trans('index.roles')</label>
                  <select class="form-control" name="role_id" id="role_id">
                    @foreach($opciones['roles'] as $key => $valueOption)
                      <option value="{{$key}}">
                        {{ $valueOption }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="form-group">
                  <label for="causante_id">@trans('index.causantes')</label>
                  <select class="form-control" name="causante_id" id="causante_id">
                    @foreach($opciones['causantes'] as $key => $valueOption)
                      <option value="{{$key}}">
                        {{ $valueOption }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="form-group">
                  <label for="destinatario_id">@trans('index.destinatario')</label>
                  <select class="form-control no-req" name="destinatario_id" id="destinatario_id">
                    @foreach($opciones['destinatarios'] as $key => $valueOption)
                      <option value="{{$key}}">
                        {{ $valueOption }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <!--Fin DNV-->
          </div>

          <div class="page-header">
            <h3 class="m-0" >@trans('forms.evento')</h3>
          </div>

          <div class="col-md-12 col-sm-12">
            <div class="form-group">
              <label class="col-md-3">
                <input type="radio" name="accion_estado" class="toggleAccionEstado" value="0" checked>@trans('index.acciones')
              </label>
              <label class="col-md-3">
                <input type="radio" name="accion_estado" class="toggleAccionEstado" value="1">@trans('index.estados')
              </label>
            </div>
          </div>
          <input name="tipo_desencadenante_id" id="tipo_desencadenante_id" class="hidden" value="{{$tipos_desencadenante['accion']}}">
          <div class="row desencadenante_id">
            <div class="col-md-12 col-sm-12">
              <div class="form-group">
                <label for="desencadenante_id">@trans('index.acciones')</label>
                <select class="form-control" name="desencadenante_id" id="desencadenante_id" required>
                  @foreach($opciones['acciones'] as $key => $valueOption)
                    <option value="{{$key}}" data-correccion="0">
                    @if($valueOption == trans('forms.select.accion'))
                      {{$valueOption}}
                    @else
                      @trans('sol_redeterminaciones.acciones.' . $valueOption)
                    @endif
                    </option>
                  @endforeach
                  {{-- @foreach($opciones['acciones_correccion'] as $key => $valueOption)
                    <option value="{{$key}}" data-correccion="1">
                      @trans('index.corregir') @trans('redeterminaciones.corregir.' . $valueOption)
                    </option>
                  @endforeach --}}
                </select>
              </div>

            </div>
            <input name="correccion" id="correccion" class="hidden" value="0">
          </div>

          <div class="row desencadenante_id hidden">
            <div class="col-md-8 col-sm-8">
              <div class="form-group">
                <label for="desencadenante_id">@trans('index.estados')</label>
                <select class="form-control" name="desencadenante_estado_id" id="desencadenante_estado_id">
                  @foreach($opciones['estados'] as $key => $valueOption)
                    <option value="{{$key}}">
                    @if($valueOption == trans('forms.select.estado'))
                      {{$valueOption}}
                    @else
                      @trans('sol_redeterminaciones.acciones.' . $valueOption)</option>
                    @endif
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-md-4 col-sm-4">
              <div class="form-group">
                <label for="desencadenante_id">@trans('forms.tiempo_espera')</label>
                <input class="form-control" type="number" min="1" id="tiempo_espera" name="tiempo_espera">
              </div>
            </div>
          </div>

          <div class="page-header">
            <h3 class="m-0" >@trans('forms.mensaje')</h3>
          </div>
          <div class="row">
            <div class="col-md-12 col-sm-12">
              <div class="form-group">
                {{ Form::label('titulo', trans('forms.titulo')) }}
                {{ Form::text('titulo', '', array('class' => 'form-control', 'required', 'placeholder' => trans('forms.titulo'))) }}
              </div>
            </div>

            <div class="col-md-12 col-sm-12">
              <div class="form-group">
                <textarea id="mensaje" name="mensaje"></textarea>
              </div>
            </div>
          </div>

          <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
            <div class="text-right">
                {{ Form::submit(trans('forms.guardar'), array('class' => 'btn btn-primary pull-right', 'id' => 'btn_guardar')) }}
            </div>
          </div>
        </form>
      </div>
    </div>
    <!--Fin panel-->
  </div>
</div>
@endsection

@section('scripts')
  var tipos_desencadenante = {!! json_encode($tipos_desencadenante) !!};
  $(document).ready(function() {
    applyToggleSelect();
    applyToggleAccionEstado();
    CKEDITOR.replace('mensaje');

    $('select[name="desencadenante_id"').on('change', function () {
      $('#correccion').val($(this).find(':selected').data('correccion'));
    });
  });

  window.applyToggleSelect = () => {
    $('input[type=radio][class=toggleSelect]').unbind('change').change(function() {
      $(".toggleHidden").toggleClass('hidden');
      $('.toggleHidden').find('select:not(.no-req)').attr('required', function (_, attr) { return !attr });
      applyAll();
    });
  }

  window.applyToggleAccionEstado = () => {
    $('input[type=radio][class=toggleAccionEstado]').unbind('change').change(function() {
      $('.desencadenante_id').toggleClass('hidden');
      $('#tiempo_espera').attr('required', function (_, attr) { return !attr });
      $('#desencadenante_id').attr('required', function (_, attr) { return !attr });
      $('#desencadenante_estado_id').attr('required', function (_, attr) { return !attr });

      // var tipo_actual = (_.invert(tipos_desencadenante))[1];
      // console.log(tipo_actual)
      if($('#tipo_desencadenante_id').val() == 1){
        var tipo_actual = 'estado';
        {{-- console.log("es estado") --}}
      }else{
         var tipo_actual = 'accion';
         {{-- console.log('es accion') --}}
      }
      if(tipo_actual == 'estado'){
        $('#tipo_desencadenante_id').val(tipos_desencadenante['estado']);
        {{-- console.log('cambio a estado') --}}
      }else{
        $('#tipo_desencadenante_id').val(tipos_desencadenante['accion']);
        {{-- console.log('cambio a accion') --}}
      }
      applyAll();
    });
  }
@endsection
