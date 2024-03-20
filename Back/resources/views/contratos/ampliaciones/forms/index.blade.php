  <div class="col-md-6 col-sm-12">
    <div class="form-group">
      <label>{{trans('forms.expediente')}}</label>
      <input type='text' value="{{$ampliacion->expediente}}" id='expediente' name='expediente' class='form-control' placeholder='{{trans('forms.expediente')}}' required>
    </div>
  </div>

  <div class="col-md-6 col-sm-12">
    <div class="form-group">
      <label>{{trans('contratos.resoluc_aprobatoria')}}</label>
      <input type='text' value="{{$ampliacion->resoluc_aprobatoria}}" id='resoluc_aprobatoria' name='resoluc_aprobatoria' class='form-control' placeholder='{{trans('contratos.resoluc_aprobatoria')}}'>
    </div>
  </div>

  <div id="datos_particulares">
    @include('contratos.ampliaciones.forms.' . $tipo_ampliacion)
  </div>

  <div class="col-md-6 col-sm-12">
    <div class="form-group form-group-chosen">
    <label for="motivo_id">{{trans('index.motivos_reprogramacion')}}</label>
    <select class="form-control" name="motivo_id" id="motivo_id">
      @foreach($motivos as $key => $value)
        <option value="{{$key}}" @if($ampliacion->motivo_id == $key) selected @endif>{{$value}}</option>
      @endforeach
    </select>
    </div>
  </div>

  <div class="col-md-12 col-sm-12">
    <div class="form-group">
      <label>@trans('index.observaciones')</label>
      <textarea class="form-control" name="observaciones" id="observaciones" placeholder="@trans('index.observaciones')">{{$ampliacion->observaciones}}</textarea>
    </div>
  </div>

  <div class="col-md-12 col-sm-12">
    <div class="form-group">
      <label for="adjunto" id="adjunto">
        <span>
         @trans('forms.adjuntos') </span>
       </label>
      <span class="format_adjuntar_poder">{{ trans('forms.formatos_validos_all') }}</span>
      <input type="file" name="adjunto[]" id="adjunto" class="file_upload" accept="image/jpeg,image/jpg,image/gif,image/png,application/pdf,.csv,.doc,.xlsx" multiple>
    </div>
    @if($ampliacion->adjuntos != null)
      @foreach($ampliacion->adjuntos as $key => $adjunto)
        <span id="adjunto_anterior" class="hide-on-ajax">
          <i class="fa fa-paperclip grayCircle"></i>
          <a download="{{$adjunto->adjunto_nombre}}" href="{{$adjunto->adjunto_link}}" id="file_item" target="_blank">{{$adjunto->adjunto_nombre}}</a>
        </span>
        <br>
      @endforeach
    @endif
  </div>
