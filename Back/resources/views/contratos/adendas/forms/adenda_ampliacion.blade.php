<div class="row">
  <form role="form" method="POST" data-action="{{route('adenda.storeUpdate')}}" id="form-ajax">
    {{ csrf_field() }}
    <input type='text' id='borrador' name='borrador' class='hidden' value="0">
    <input type='text' id='id' name='id' class='hidden' value="{{$contrato->id}}">
    <input type='text' id='contrato_padre_id' name='contrato_padre_id' class='hidden' value="{{$contrato_padre->id}}">
    <input type='text' id='tipo_id' name='tipo_id' class='hidden' value="{{$tipo_contrato->id}}">

    <div class="alert alert-danger hidden">
      <ul> </ul>
    </div>

    <div class="col-md-4 col-sm-12">
      <div class="form-group">
        <label>{{trans('forms.expediente')}}</label>
        <input type='text' value="{{$contrato->expediente_madre}}" id='expediente_madre' name='expediente_madre' class='form-control' placeholder='{{trans('forms.expediente')}}' required>
      </div>
    </div>

    <div class="col-md-12 col-sm-12">
      <div class="form-group">
        <label>{{trans('index.denominacion')}}</label>
        <input type='text' value="{{$contrato->denominacion}}" id='denominacion' name='denominacion' class='form-control' placeholder='{{trans('index.denominacion')}}'>
      </div>
    </div>

    <div class="col-md-6 col-sm-12">
      <div class="form-group">
        <label>{{trans('contratos.fecha_aprobacion')}}</label>
        <input type='text' value="{{$contrato->fecha_aprobacion}}" id='fecha_aprobacion' name='fecha_aprobacion' class='form-control input-datepicker-m-y-up-to-today' placeholder='{{trans('contratos.fecha_aprobacion')}}'>
      </div>
    </div>

    <div class="col-md-6 col-sm-12">
      <div class="form-group">
        <label>{{trans('contratos.resoluc_adjudic')}}</label>
        <input type='text' value="{{$contrato->resoluc_adjudic}}" id='resoluc_adjudic' name='resoluc_adjudic' class='form-control' placeholder='{{trans('contratos.resoluc_adjudic')}}'>
      </div>
    </div>

    @if($contrato->id == null || $contrato->borrador)
      @foreach($contrato_padre->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
        <div class="col-md-6 col-sm-12">
          <div class="form-group">
            <label>{{trans('adendas.monto_total_basico')}} {{$valueContratoMoneda->moneda->nombre_simbolo}}</label>
            <input type="text" class="form-control currency" value="{{$contrato->montoAdenda($contrato->id, $valueContratoMoneda->moneda->id)}}" id='monto_inicial_{{$valueContratoMoneda->id}}' name='monto_inicial[{{$valueContratoMoneda->id}}]' placeholder='@trans('adendas.monto_total_basico') {{$valueContratoMoneda->moneda->nombre_simbolo}}'>
            <small class="msg_sugerencia_input text-success">{{trans('adendas.completar_monto')}}</small>
          </div>
        </div>
      @endforeach
    @endif

    <div class="col-md-12 col-sm-12 col-xs-12 p-0">
      <div class="form-group mb-1">
        <label class="fixMargin4">
          <div class="checkbox noMarginChk">
            <div class="btn-group chk-group-btn" data-toggle="buttons">
              <label class="btn btn-primary btn-sm @if($contrato->requiere_garantia) active @endif">
                <input autocomplete="off" class="triggerClickChk" type="checkbox" name="requiere_garantia" id="requiere_garantia"
                @if($contrato->requiere_garantia) checked @endif>
                <span class="glyphicon glyphicon-ok"></span>
              </label>
              {!!trans('contratos.requiere_garantia')!!}
            </div>
          </div>
        </label>
      </div>
    </div>

    <div class="col-md-6 col-sm-12">
      <div class="form-group">
        <label>@trans('contratos.fecha_acta_inicio')</label>
        <input type='text' value="{{$contrato->fecha_acta_inicio}}" id='fecha_acta_inicio' name='fecha_acta_inicio' class='form-control input-datepicker-m-y' placeholder='@trans('contratos.fecha_acta_inicio')'>
      </div>
    </div>

    @if($contrato->id == null || $contrato->borrador)
      <div class="col-md-12 col-sm-12 error_rad_simple_compuesto">
        <div class="form-group" id="radio_title">
          <label>{{trans('contratos.plazo_obra')}}</label>
        </div>
      </div>

      <div class="col-md-12 col-sm-12 radio-options">
        <div class="container_input_check mb-1">
          <div class="col-md-6 col-sm-12">
            <div class="form-group" id="div_simple_compuesto">
              @foreach($plazos as $key => $value)
              <label class="col-md-6" id="simple">
                <input type="radio" name="plazo_id" class="toggleSelect" value="{{$value->id}}"
                @if($value->id == $contrato_padre->plazo_id) checked @endif
                  @if($value->id != $contrato_padre->plazo_id) disabled @endif>
                  {{trans_choice('contratos.plazo.' . $value->nombre, 2)}}
              </label>
              @endforeach
            </div>
          </div>
          <div class="col-md-6 col-sm-12 col-xs-12 p-0">
            <div class="form-group">
              <input type='number' value="{{$contrato->plazo}}"id='plazo' name='plazo' class='form-control' placeholder='{{trans('contratos.plazo_obra')}}'>
              <small class="msg_sugerencia_input text-success">{{trans('adendas.completar_plazo')}}</small>
            </div>
          </div>
        </div>
      </div>
    @endif

    @if($contrato->adjuntos != null)
      @foreach($contrato->adjuntos as $key => $adjunto)
        <span id="adjunto_anterior" class="hide-on-ajax">
          <i class="fa fa-paperclip grayCircle"></i>
          <a download="{{$adjunto->adjunto_nombre}}" href="{{$adjunto->adjunto_link}}" id="file_item" target="_blank">{{$adjunto->adjunto_nombre}}</a>
        </span>
        <br>
      @endforeach
    @endif

    <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
      <div class="text-right">
        <a class="btn btn-small btn-success" href="{{ route('contratos.index') }}">@trans('forms.volver')</a>
        {{ Form::submit(trans('forms.guardar'), array('class' => 'btn btn-primary pull-right btn_guardar')) }}
        @if($contrato->id == null || $contrato->borrador)
          {{ Form::submit(trans('forms.guardar_borrador'), array('class' => 'btn btn-basic pull-right borrador')) }}
        @endif
      </div>
    </div>
  </form>

</div>
