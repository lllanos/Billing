@extends ('layout.app')

@section('title', config('app.name'))

@section('content')
<div class="row">
  <div class="col-md-12">
    <ol class="breadcrumb">
      <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
      <li><a href="{{ route('redeterminaciones.index') }}">@trans('index.solicitudes_redeterminacion')</a></li>
      <li class="active">@trans('index.solicitar_redeterminacion')</li>
    </ol>
    <div class="page-header">
      <h3>@trans('index.solicitar_redeterminacion')</h3>
    </div>

    <div class="panel panel-default">
      <div class="panel-body">
        <form method="POST" data-action="{{ route('solicitudes.redeterminaciones.solicitar.post') }}" enctype="multipart/form-data" id="form-ajax">
          {{ csrf_field() }}
          @if(isset($user_contrato))
            <h4>@trans('index.contrato') {{$user_contrato->contrato->nombre}}</h4>
            <input class="hidden" value="{{$user_contrato->contrato->id}}" name="contrato_id" id="contrato_id">
          @else
            <div class="row">
              <div class="col-md-12 col-sm-12">
                <div class="form-group">
                  <label for="contrato_id">@trans('index.contrato')</label>
                  <select class="form-control select-contratos" name="contrato_id" id="contrato_id" data-action="{{route('html.GetSaltos', ['id' => ':id'])}}" required>
                    <option disabled selected value> {{trans('forms.select.contrato')}}</option>
                      @foreach(Auth::user()->user_publico->mis_contratos_select as $opcion)
                        <option value="{{$opcion['id']}}" >{{$opcion['value']}} </option>
                      @endforeach
                  </select>
                </div>
              </div>
            </div>
          @endif
          <div id="saltos-list" class="col-md-12 col-sm-12">
            @if(isset($user_contrato))
              @include('redeterminaciones.solicitudes.saltos')
            @endif
          </div>
          <div class="row">
            <div class="col-md-12 col-sm-12">
              <div class="form-group">
                {{ Form::label('observaciones', trans('index.observaciones')) }}
                {{ Form::textarea('observaciones', '', array('placeholder' => trans('index.observaciones'), 'class' => 'form-control', 'id'=>'observaciones')) }}
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="form-group">
                  {{ Form::label('adjunto', trans('forms.adjunto')) }} <span class="format_adjuntar_poder">@trans('forms.formatos_validos_poder')</span>
                  <input type="file" name="adjunto" id="adjunto" class="file_upload" accept="image/jpeg,image/jpg,image/gif,image/png,application/pdf">
                </div>
            </div>
          </div>

          <input class="hidden" value="0" name="btn_disabled_chk" id="btn_disabled_chk">

          <div class="col-md-10 col-md-offset-1 text-center">
            <div class="form-group">
              <label class="fixMargin4">
                <div class="checkbox noMarginChk m-0">
                  <div class="btn-group chk-group-btn" data-toggle="buttons">
                    <label class="btn btn-primary btn-sm" for="chk_ddjj_redeterminar" id="chk_ddjj_redeterminar_label">
                      <input autocomplete="off" class="triggerClickChk" type="checkbox" name="chk_ddjj_redeterminar" id="chk_ddjj_redeterminar">
                      <span class="glyphicon glyphicon-ok"></span>
                    </label>
                    @trans('sol_redeterminaciones.declaro.texto')
                    <a href="#" data-target="#modal_ddjj" data-toggle="modal" class="modal-ddjj">@trans('sol_redeterminaciones.declaro.link')</a>
                  </div>
                </div>
              </label>
            </div>
          </div>
          <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
            <div class="text-right">
              <a class="btn btn-small btn-success" href="{{ route('redeterminaciones.index') }}">@trans('forms.volver')</a>
                {{ Form::submit(trans('forms.guardar'), array('class' => 'btn btn-primary pull-right', 'disabled', 'id' => 'btn_guardar')) }}
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('modals')
  @include('ddjj.body')
@endsection

@section('scripts')
  $(document).ready(function() {
    $('.select-contratos').unbind('change');
    $('.select-contratos').on('change', function() {
      var action = $(this).data('action');
      action = action.replace(':id', $(this).find(":selected").val());

      loadingToggle();
      $.ajax({
        url: action,
        type: 'GET',
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function(resp) {
          loadingToggle();
          if(resp.status == true) {
            if(resp.html != undefined) {
              $('#saltos-list').html('').append(resp.html);
            }
            toggleEnableSave();
          } else {
            if(resp.message.length > 0)
              modalCloseToastError(resp.message);
          }
        }
      });
    });

    $('#chk_ddjj_redeterminar_label').on('click', function () {
      if($('#btn_disabled_chk').val() == 1) {
        $('#btn_disabled_chk').val(0);
      } else {
        $('#btn_disabled_chk').val(1);
      }

      toggleEnableSave();
    });

    window.toggleEnableSave = () => {
       if($('#btn_disabled_chk').val() == 1 && $('#btn_disabled').length > 0 && $('#btn_disabled').val() == 0) {
        $('#btn_guardar').prop('disabled', false);
      } else {
        $('#btn_guardar').prop('disabled', true);
      }
    }

  });

@endsection
