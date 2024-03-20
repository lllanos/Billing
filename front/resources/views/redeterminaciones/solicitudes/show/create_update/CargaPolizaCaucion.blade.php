<input type="hidden" name="js_applied" id="js_applied" value="0">
<div class="modal-header">
  <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true" class="fa fa-times fa-2x"></span>
  </button>
  <h4 class="modal-title">
    {!!trans('sol_redeterminaciones.acciones.' . $instancia)!!}
  </h4>
</div>

<form method="POST" data-action="{{ route('solicitudes.update.store', ['instancia' => $instancia, 'id_solicitud' => $redeterminacion->id, 'correccion' => $correccion]) }}" id="form-solicitud-ajax" enctype="multipart/form-data">
  {{ csrf_field() }}
  <div class="modal-body">
    <div class="modalContentScrollable">
      <div class="row">
        <div class="col-md-12">
          <div class="alert alert-info" role="alert">
            <span class="alert-link">{{trans('sol_redeterminaciones.titulo_pasos')}}</span>
            <ul class="modal_lista">
              @foreach(trans('sol_redeterminaciones.pasos.' . $instancia) as $key => $value)
                <li class="modal_lista_item">{{$value}}</li>
              @endforeach
            </ul>
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <label>{{trans('forms.select_create.poliza')}}</label>
            <input type='text' id='poliza' name='poliza' class='form-control'>
          </div>
        </div>

        <div class="col-md-12 polizas-anteriores hidden">
          <div class="form-group">
            </label>
          </div>
        </div>

        <div class="col-md-12 poliza-nueva hidden">
          <div class="form-group">
          </div>
        </div>

        <input class="hidden" id="new" name="new">

        <div class="col-md-12">
          <div class="form-group">
            <label>{{trans('index.observaciones')}}</label>
            <textarea class="form-control" name="observaciones"></textarea>
          </div>
        </div>

      </div>
    </div>
  </div>
  <div class="modal-footer no-padding-bottom">
    <div class="col-md-12">
      <button type="submit" class="btn btn-primary pull-right" id="btn_guardar">@trans('index.guardar')</button>
    </div>
  </div>
</form>

<script type="text/javascript">
  $('#poliza').selectize({
      persist: true,
      maxItems: 1,
      valueField: 'id',
      labelField: 'id',
      delimiter: '|||',
      searchField: 'id',
      options: {!! json_encode($redeterminacion->opciones_poliza) !!},
      render: {
          item: function(item, escape) {
            if(item.poliza == undefined) {
              $('#new').val(1);
              var poliza = item.id;

              $('.poliza-nueva').removeClass('hidden');

              let adjunto_html=`<span id="adjunto_nuevo">
              <label></label>
              <input type="file" name="adjuntar_poliza" id="adjuntar_poliza" class="file_upload" accept="image/jpeg,image/jpg,image/gif,image/png,application/pdf" required>
              </span>`;

              //Borra el adjunto anterior y oculta el contenedor anterior
              if($('.polizas-anteriores').find('#adjunto_anterior')) {
                $('.polizas-anteriores').find('#adjunto_anterior').remove();
                $('.polizas-anteriores').addClass('hidden');
              }
              //Si existe el input nuevo lo elimina para agregar otro
              if($('.poliza-nueva').find('#adjuntar_poliza')) {
                $('.poliza-nueva').find('#adjunto_nuevo').remove();//Elimina el contenedor del input
              }
              $('.poliza-nueva').find('.form-group').append(adjunto_html);
              $('.poliza-nueva').find('label').text("{{trans('forms.adjuntar_poliza')}}" + ' ' + item.id+' *');
              applyFileInput();
              applyRequired();
            } else {
              $('#new').val(0);
              var poliza = item.poliza;

              $('.polizas-anteriores').removeClass('hidden');
              $('.polizas-anteriores').find('#descripcion_anterior').text(item.poliza);

              let adjunto_html = '<span id="adjunto_anterior">' +
                                  '<i class="fa fa-paperclip grayCircle"></i>' +
                                    '<a download=" ' + item.adjunto_nombre + ' " href=" ' + item.adjunto_link + ' " id="file_item" target="_blank"> ' + item.adjunto_nombre + ' </a>' +
                                  '</span>';
              //Si existe el input nuevo lo elimina
              if($('.poliza-nueva').find('#adjuntar_poliza')) {
                $('.poliza-nueva').find('#adjunto_nuevo').remove();//Elimina el contenedor del input
                $('.poliza-nueva').addClass('hidden');
              }
              //Si existe una poliza anterior la elimina por una nueva
              if($('.polizas-anteriores').find('#adjunto_anterior')) {
                $('.polizas-anteriores').find('#adjunto_anterior').remove(); //Elimina la poliza agregada
              }
              $('.polizas-anteriores').find('.form-group').append(adjunto_html);
            }

            return '<div><span class="selectize-seleccionada">' + escape(poliza) + '</span></div>';

            return '';
          },
          option: function(item, escape) {
              var label = item.poliza;
              return '<div>' +
                  (item.poliza ? '<span class="email">' + escape(item.poliza) + '</span>' : '') +
              '</div>';
          },
          option_create: (data, escape) => {
             return '<div class="create">' +"{{ trans('sol_redeterminaciones.nueva_poliza') }}" + ': <strong>' + escape(data.input) + '</strong></div>';
           }
      },
      create: function(input) {
               return {id: input};
      },
      onChange: function(value) {
        $(".selectize-input input[placeholder]").attr("style", "width: 100%;");
      },
  });
  $(".selectize-input input[placeholder]").attr("style", "width: 100%;");
</script>
