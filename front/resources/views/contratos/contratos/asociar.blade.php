@extends ('layout.app')

@section('title', config('app.name'))

@section('content')
<div class="row">
  <div class="col-md-12">
    <ol class="breadcrumb">
      <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
      <li><a href="{{route('contratos.index')}}">@trans('forms.contratos')</a></li>
      <li class="active">@trans('index.solicitar_asociacion')</li>
    </ol>
    <div class="page-header">
      <h3>
        @trans('index.solicitar_asociacion')
      </h3>
    </div>

			<div class="panel panel-default">
			  <div class="panel-body">
          <form method="POST" data-action="{{ route('contrato.asociar.post') }}" enctype="multipart/form-data" id="form-confirmable">
            {{ csrf_field() }}

            <div class="row">
              <div class="col-md-12 col-sm-12">
                <div class="form-group">
                  <label for="contrato_id">@trans('index.contrato')</label>
                  <select class="form-control" name="contrato_id" id="contrato_id" required>
                    <option disabled selected value> @trans('forms.select.contrato')</option>
                    @foreach(Auth::user()->user_publico->contratos_select as $opcion)
                      <option value="{{$opcion['id']}}">{{$opcion['value']}} </option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>

            <div class="row descripcion_redeterminacion">
  			      <div class="col-md-12 col-sm-12">
  			        <div class="form-group">
  			          {{ Form::label('descripcion', trans('forms.descripcion')) }}
  			          {{ Form::text('descripcion', Input::old('descripcion'), array('class' => 'form-control', 'required', 'autofocus', 'placeholder' => trans('forms.descripcion'))) }}
  			        </div>
  			      </div>
            </div>
            <div class="row redetermina-auto hidden">
              <div class="col-md-12 col-sm-12 ">
                  <div class="btn-group chk-group-btn pt-0" data-toggle="buttons">
                    <label class="btn btn-primary btn-sm active">
                      <input type="checkbox" autocomplete="off" name="solicitar_redeterminacion_auto" checked id="solicitar_redeterminacion_auto">
                      <span class="glyphicon glyphicon-ok"></span>
                    </label>
                    @trans('index.solicitar_redeterminacion_auto')
                  </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12 col-sm-12">
                <div class="form-group">
                  <label class="col-md-3">
                    <input type="radio" name="apoderado_representante" class="toggleLabels" value="0" checked>@trans('forms.apoderado')
                  </label>
                  <label class="col-md-3">
                    <input type="radio" name="apoderado_representante" class="toggleLabels" value="1">@trans('forms.representante')
                  </label>
                </div>
              </div>
            </div>

            <div class="row">
               <div class="col-md-9 col-sm-12">
                 <div class="form-group">
                   <label for="adjuntar_poder" id="label_adjuntar_poder">
                     <span class =" toggleable" id=""> @trans('forms.adjuntar_poder')  </span>
                      <span class ="hidden toggleable" id="">@trans('forms.adjuntos_representante') </span>
                    </label>
                   <span class="format_adjuntar_poder">@trans('forms.formatos_validos_poder')</span>
                   <input type="file" name="adjuntar_poder[]" id="adjuntar_poder" class="file_upload" accept="image/jpeg,image/jpg,image/gif,image/png,application/pdf" required>
                 </div>
               </div>

               <div class="col-md-3 col-sm-12">
                 <div class="form-group">
                   <label for="fecha_fin_poder">
                     <span class =" toggleable" id="">
                      @trans('forms.vigencia_poder') </span>
                      <span class ="hidden toggleable" id="">
                       @trans('forms.vigencia_acta_nombramiento') * </span>
                    </label>
                   {{ Form::text('fecha_fin_poder', '', array('class' => 'form-control input-datepicker-m-y', 'id'=>'fecha_fin_poder', 'placeholder' => trans('forms.vigencia_poder'))) }}
                 </div>
               </div>
            </div>

            <div class="row">
               <div class="col-md-12 col-sm-12">
                 <div class="form-group">
                   {{ Form::label('observaciones', trans('index.observaciones')) }}
                   {{ Form::textarea('observaciones', '', array('placeholder' => trans('index.observaciones'), 'class' => 'form-control', 'id'=>'observaciones')) }}
                 </div>
               </div>
             </div>
             <div class="error_campos_requeridos text-center">

             </div>
             <input type="hidden" name="confirmado" id="confirmado" value="0">

             <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
               <div class="text-right">
                 <a class="btn btn-small btn-success" href="{{ route('contrato.solicitudes') }}">@trans('forms.volver')</a>
                 <a id="btn_guardar" href="#" data-target="#modal_ddjj" data-toggle="modal" class="btn btn-primary pull-right modal-ddjj">{{trans('forms.guardar')}}</a>
                </div>
             </div>

			    </form>
			  </div>
			</div>
	  </div>
	</div>
@endsection

@section('modals')
  @include('contratos.contratos.checklist_asociar')
@endsection

@section('scripts')
  $(document).ready( () => {
    const htmlCamposRequeridos = `<span class='mostrar_error_campos_req text-danger'>{{trans('forms.todos_campos_requeridos')}}</span>`;
    $("#btn_guardar").on('click', function(){
      let valid;
      $('.mostrar_error_campos_req').remove();
      if(!$("#form-confirmable")[0].checkValidity()){
        $('.error_campos_requeridos').append(htmlCamposRequeridos);
        valid = false;
      }else{
        valid = true;
      }
      return valid;
    })

    $('label.active').click(false);

    var total = $('.chk-declaro').length;
    var aceptados = 0;
    $('.chk-declaro-label').on('click', function () {
      if(!$(this).hasClass('active')) {
        aceptados++;
      } else {
        aceptados--;
      }
      if(aceptados == total) {
        $('#btn_asociar').attr('disabled', false);
      } else {
        $('#btn_asociar').attr('disabled', true);
      }
    });

    applyFormChecklistAjax();
    applyToggleRepresentante();
  });

  window.applyFormChecklistAjax = () => {
    $('.asociar-modal').unbind('click').on('click', function(e) {
      if($(this).attr('disabled') != undefined)
        return false;
      $('.help-block').remove();
      $('.form-group').removeClass('has-error');
      e.preventDefault();
      if(!$('body').find('.gralState').hasClass('state-loading'))
          loadingToggle();

      var action = $('#form-confirmable').data('action');
      var formData = new FormData(document.forms['form-confirmable']);
      var modalFormData = jQuery(document.forms['form-ddjj']).serializeArray();
      for(var i=0; i < modalFormData.length; i++)
        formData.append(modalFormData[i].name, modalFormData[i].value);

      

      $.ajax({
        url: action,
        enctype: 'multipart/form-data',
        type: 'POST',        
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        success: function(resp) {
          if(resp.status == true) {
            if(resp.message != undefined)
              modalCloseToastSuccess(resp.message);
            if(resp.url != undefined) {
              window.location.href = resp.url;
            }
            if(resp.refresh != undefined && resp.refresh == true) {
              location.reload();
            }
            if(resp.a_confirmar) {
              if($('body').find('.gralState').hasClass('state-loading'))
                  loadingToggle();
              $('.footer-confirm').toggleClass('hidden');
              $('#confirmado').val(1)
              $('.message-confirm').html(resp.conf_message);
            }
          } else {
            $('#modal_ddjj').modal('hide');

            if($('body').find('.gralState').hasClass('state-loading'))
                loadingToggle();

            if(Object.keys(resp.errores).length > 0) {
              mostrarErroresEnInput(resp.errores);
            }

            if(resp.message.length > 0)
              modalCloseToastError(resp.message);

            window.scrollTo($('#form-confirmable').position());
          }
        },
        error: function (data) {
            console.log(data.status);
        }
      });
    });
  }

  var applyToggleRepresentante = () => {
    $('input[type=radio][class=toggleLabels]').unbind('change').change(function() {
      $('.toggleable').toggleClass('hidden')
      $('#fecha_fin_poder').attr('required', function (_, attr) { return !attr });
      applyAll();
    });
  }

@endsection
