@if($edit)
<div class="panel-body">
  <div class="row">
    <div class="col-md-6 col-sm-6">
      <div class="form-group">
        <label for="acta_medicion" id="acta_medicion">
          <span>
           @trans('certificado.adjuntos.acta_medicion') *</span>
         </label>
        <span class="format_adjuntar_poder">{{ trans('forms.formatos_validos_all') }}</span>
        <input type="file" name="acta_medicion" id="acta_medicion" class="file_upload" accept="image/jpeg,image/jpg,image/gif,image/png,application/pdf,.csv,.doc,.xlsx" multiple>
        @if($certificado->acta_medicion != null)
          <span id="adjunto_anterior" class="hide-on-ajax">
            <i class="fa fa-paperclip grayCircle"></i>
            <a download="{{$certificado->acta_medicion->adjunto_nombre}}" href="{{$certificado->acta_medicion->adjunto_link}}" id="file_item" target="_blank">{{$certificado->acta_medicion->adjunto_nombre}}</a>
          </span>
        @endif
      </div>      
    </div>
    <div class="col-md-6 col-sm-6">
      <div class="form-group">
        <label for="seguro_responsabilidad_civil" id="seguro_responsabilidad_civil">
          <span>
           @trans('certificado.adjuntos.seguro_responsabilidad_civil') *</span>
         </label>
        <span class="format_adjuntar_poder">{{ trans('forms.formatos_validos_all') }}</span>
        <input type="file" name="seguro_responsabilidad_civil" id="seguro_responsabilidad_civil" class="file_upload" accept="image/jpeg,image/jpg,image/gif,image/png,application/pdf,.csv,.doc,.xlsx" multiple>
        @if($certificado->seguro_civil != null)
          <span id="adjunto_anterior" class="hide-on-ajax">
            <i class="fa fa-paperclip grayCircle"></i>
            <a download="{{$certificado->seguro_civil->adjunto_nombre}}" href="{{$certificado->seguro_civil->adjunto_link}}" id="file_item" target="_blank">{{$certificado->seguro_civil->adjunto_nombre}}</a>
          </span>
        @endif
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6 col-sm-6">
      <div class="form-group">
        <label for="seguro_vida" id="seguro_vida">
          <span>
           @trans('certificado.adjuntos.seguro_vida') *</span>
         </label>
        <span class="format_adjuntar_poder">{{ trans('forms.formatos_validos_all') }}</span>
        <input type="file" name="seguro_vida" id="seguro_vida" class="file_upload" accept="image/jpeg,image/jpg,image/gif,image/png,application/pdf,.csv,.doc,.xlsx" multiple>
        @if($certificado->seguro_vida != null)
          <span id="adjunto_anterior" class="hide-on-ajax">
            <i class="fa fa-paperclip grayCircle"></i>
            <a download="{{$certificado->seguro_vida->adjunto_nombre}}" href="{{$certificado->seguro_vida->adjunto_link}}" id="file_item" target="_blank">{{$certificado->seguro_vida->adjunto_nombre}}</a>
          </span>
        @endif
      </div>
    </div>
    <div class="col-md-6 col-sm-6">
      <div class="form-group">
        <label for="ART" id="ART">
          <span>
           @trans('certificado.adjuntos.ART') *</span>
         </label>
        <span class="format_adjuntar_poder">{{ trans('forms.formatos_validos_all') }}</span>
        <input type="file" name="ART" id="ART" class="file_upload" accept="image/jpeg,image/jpg,image/gif,image/png,application/pdf,.csv,.doc,.xlsx" multiple>
        @if($certificado->art != null)
          <span id="adjunto_anterior" class="hide-on-ajax">
            <i class="fa fa-paperclip grayCircle"></i>
            <a download="{{$certificado->art->adjunto_nombre}}" href="{{$certificado->art->adjunto_link}}" id="file_item" target="_blank">{{$certificado->art->adjunto_nombre}}</a>
          </span>
        @endif
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6 col-sm-6">
      <div class="form-group">
        <label for="nueve_tres_uno" id="nueve_tres_uno">
          <span>
           @trans('certificado.adjuntos.nueve_tres_uno') </span>
         </label>
        <span class="format_adjuntar_poder">{{ trans('forms.formatos_validos_all') }}</span>
        <input type="file" name="nueve_tres_uno" id="nueve_tres_uno" class="file_upload" accept="image/jpeg,image/jpg,image/gif,image/png,application/pdf,.csv,.doc,.xlsx" multiple>
        @if($certificado->nueve_tres_uno != null)
            <span id="adjunto_anterior" class="hide-on-ajax">
              <i class="fa fa-paperclip grayCircle"></i>
              <a download="{{$certificado->nueve_tres_uno->adjunto_nombre}}" href="{{$certificado->nueve_tres_uno->adjunto_link}}" id="file_item" target="_blank">{{$certificado->nueve_tres_uno->adjunto_nombre}}</a>
            </span>
        @endif
      </div>  
    </div>
    <div class="col-md-6 col-sm-6">
      <div class="form-group">
        <label for="adjunto" id="adjunto">
          <span>
           @trans('forms.adjuntos') </span>
         </label>
        <span class="format_adjuntar_poder">{{ trans('forms.formatos_validos_all') }}</span>
        <input type="file" name="adjunto" id="adjunto" class="file_upload" accept="image/jpeg,image/jpg,image/gif,image/png,application/pdf,.csv,.doc,.xlsx" multiple>
        @if($certificado->otros_adjuntos != null)
          <span id="adjunto_anterior" class="hide-on-ajax">
            <i class="fa fa-paperclip grayCircle"></i>
            <a download="{{$certificado->otros_adjuntos->adjunto_nombre}}" href="{{$certificado->otros_adjuntos->adjunto_link}}" id="file_item" target="_blank">{{$certificado->otros_adjuntos->adjunto_nombre}}</a>
          </span>
        @endif
      </div>
    </div>
  </div>
</div>
@else
<div class="panel-body">  
  <div class="col-md-12 col-sm-12">    
      @if($certificado->acta_medicion != null)    
        <label for="acta_medicion" id="acta_medicion">
          <span>
           @trans('certificado.adjuntos.acta_medicion') </span>
        </label>
        <span id="adjunto_anterior" class="hide-on-ajax">
          <i class="fa fa-paperclip grayCircle"></i>
          <a download="{{$certificado->acta_medicion->adjunto_nombre}}" href="{{$certificado->acta_medicion->adjunto_link}}" id="file_item" target="_blank">{{$certificado->acta_medicion->adjunto_nombre}}</a>
        </span>
      @endif
      <br>      
      @if($certificado->seguro_civil != null)
        <label for="seguro_responsabilidad_civil" id="seguro_responsabilidad_civil">
          <span>
           @trans('certificado.adjuntos.seguro_responsabilidad_civil') </span>
        </label>
        <span id="adjunto_anterior" class="hide-on-ajax">
          <i class="fa fa-paperclip grayCircle"></i>
          <a download="{{$certificado->seguro_civil->adjunto_nombre}}" href="{{$certificado->seguro_civil->adjunto_link}}" id="file_item" target="_blank">{{$certificado->seguro_civil->adjunto_nombre}}</a>
        </span>
      @endif        
      <br>             
      @if($certificado->seguro_vida != null)
        <label for="seguro_vida" id="seguro_vida">
          <span>
           @trans('certificado.adjuntos.seguro_vida') </span>
        </label>
        <span id="adjunto_anterior" class="hide-on-ajax">
          <i class="fa fa-paperclip grayCircle"></i>
          <a download="{{$certificado->seguro_vida->adjunto_nombre}}" href="{{$certificado->seguro_vida->adjunto_link}}" id="file_item" target="_blank">{{$certificado->seguro_vida->adjunto_nombre}}</a>
        </span>
      @endif        
      <br>  
      @if($certificado->art != null)
        <label for="ART" id="ART">
          <span>
           @trans('certificado.adjuntos.ART') </span>
        </label>        
        <span id="adjunto_anterior" class="hide-on-ajax">
          <i class="fa fa-paperclip grayCircle"></i>
          <a download="{{$certificado->art->adjunto_nombre}}" href="{{$certificado->art->adjunto_link}}" id="file_item" target="_blank">{{$certificado->art->adjunto_nombre}}</a>
        </span>
      @endif        
      <br>
      @if($certificado->nueve_tres_uno != null)
        <label for="nueve_tres_uno" id="nueve_tres_uno">
          <span>
           @trans('certificado.adjuntos.nueve_tres_uno') </span>
        </label>
        <span id="adjunto_anterior" class="hide-on-ajax">
          <i class="fa fa-paperclip grayCircle"></i>
          <a download="{{$certificado->nueve_tres_uno->adjunto_nombre}}" href="{{$certificado->nueve_tres_uno->adjunto_link}}" id="file_item" target="_blank">{{$certificado->nueve_tres_uno->adjunto_nombre}}</a>
        </span>
      @endif        
      <br>   
      @if($certificado->otros_adjuntos != null)
        <label for="adjunto" id="adjunto">
          <span>
           @trans('forms.adjuntos') </span>
        </label>
        <span id="adjunto_anterior" class="hide-on-ajax">
          <i class="fa fa-paperclip grayCircle"></i>
          <a download="{{$certificado->otros_adjuntos->adjunto_nombre}}" href="{{$certificado->otros_adjuntos->adjunto_link}}" id="file_item" target="_blank">{{$certificado->otros_adjuntos->adjunto_nombre}}</a>
        </span>
      @endif
  </div>
</div>
@endif