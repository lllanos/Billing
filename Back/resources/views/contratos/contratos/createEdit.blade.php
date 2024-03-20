@extends(isset($isAdenda) ? 'layout.appajax' : 'layout.app')

@section('title', config('app.name'))

  @section('content')
    @if(!isset($isAdenda))
      <div class="row">
        <div class="col-md-12 col-sm-12">
          @if(!isset($isAdenda))
            <ol class="breadcrumb">
              <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
              <li><a href="{{route('contratos.index')}}">@trans('index.contratos')</a></li>
              <li class="active"> @if($contrato->id == null) @trans('forms.nuevo') @else @trans('forms.editar') @endif @trans('forms.contrato')</li>
            </ol>
          @endif
          <div class="page-header">
            <h3 class="page_header__titulo">
              <div class="titulo__contenido">
                @if($contrato->id == null)
                  @trans('forms.nuevo') @trans('forms.contrato')
                @else
                  @trans('forms.editar') @trans('forms.contrato')
                @endif
              </div>
              <div class="buttons-on-title">
                @permissions(('contrato-view'))
                  @if($contrato->id != null)
                    <div class="button_desktop">
                      <a class="btn btn-success pull-right" href="{{route('contratos.ver', ['id' => $contrato->id]) }}">
                        @trans('index.ver') @trans('index.contrato')
                      </a>
                    </div>
                    <div class="button_responsive">
                      <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                          <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu pull-right">
                          <li>
                            <a href="{{route('contratos.ver', ['id' => $contrato->id]) }}">
                              @trans('index.ver') @trans('index.contrato')
                            </a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  @endif
                @endpermission
              </div>
            </h3>
          </div>
        </div>
      </div>
    @endif

    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-body">
          @include('contratos.contratos.createEditContent')
        </div>
      </div>
    </div>

  @endsection

@section('js')
<script>
  $(document).ready(() => {
    applyEditFecha();
    applyCloneMoneda();
    applyRemoveMoneda();
    applyAll();
    applyTasaCambio();

    $('.btn_guardar').on('click', function() {
      setTimeout(function(){
        chosenErrorAjax();
      }, 1000);
    });
  });


  applyEditFecha = () => {
    var inicial = $("#estado_id").val();
      if(inicial !== "2") {
          $("#new_fecha_inicio").hide();
      }

    $("#estado_id").change(function() {
      var option = $(this).val();
      if(option === "2") {
          $("#new_fecha_inicio").show();
      }
      else{
          $("#new_fecha_inicio").hide();
      }
    });

    applyAll();
  };

  applyEditFecha = () => {    
    var inicial = $("#estado_id").val();
      if(inicial !== "2") {
          $("#new_fecha_inicio").hide();
      }  

    $("#estado_id").change(function() {
      var option = $(this).val();
      if(option === "2") {
          $("#new_fecha_inicio").show();          
      }
      else{
          $("#new_fecha_inicio").hide();
      }
    });  

    applyAll();
  };

  applyRemoveMoneda = () => {
    $('.remove_button').off('click').on('click', function(e) {
      e.preventDefault();
      $(this).parents('div.can-delete').remove();
      $('.add_button').data('id', $('.add_button').data('id') - 1);
      applyCloneMoneda();
    });
  };

  applyCloneMoneda = () => {
    $('.add_button').unbind('click').click(function(e) {
      e.preventDefault();
      var id = $(this).data('id') + 1;

      if($(this).parents('div.clone-container').hasClass('can-delete'))
        $(this).parent().find('.remove_button').removeClass('hidden');

      var hidden = " ";

      const htmlTemplate = `
          <div class="col-md-12 col-sm-12 clone-container radio-options wrapper_${id} can-delete z-index-auto">
            <div class="col-md-4 col-sm-12">
              <div class="form-group form-group-chosen m-0">
              <label for="moneda_id[${id}]">{{trans('forms.moneda')}}</label>
              <select class="form-control moneda_id" name="moneda_id[${id}]" id="moneda_id_${id}">
                @foreach($monedas as $key => $value)
                  <option value="{{$key}}" @if (strpos($value, '(USD)') !== false) data-dolar="1" @else data-dolar="0" @endif>{{$value}}</option>
                @endforeach
              </select>
              </div>
            </div>

            <div class="col-md-4 col-sm-12">
              <div class="form-group">
                <label>{{trans('contratos.monto_basico')}}</label>
                <input type="text" class="form-control currency" id='monto_inicial_1${id}' name='monto_inicial[${id}]' value="" placeholder="{{trans('contratos.monto_basico')}}">
                @if((isset($isAdenda) && $contrato->is_adenda_ampliacion))
                  <small class="msg_sugerencia_input text-success">{{trans('adendas.completar_monto')}}</small>
                @endif
              </div>
            </div>

            @if(!isset($isAdenda))
              <div class="col-md-4 col-sm-12">
                <div class="form-group">
                  <label>@trans('contratos.tasa_cambio')</label>
                  <input type="text" class="form-control currency" id='tasa_cambio_${id}' name='tasa_cambio[${id}]' value="" placeholder="@trans('contratos.tasa_cambio')">
                </div>
              </div>
            @endif

            <a href="javascript:void(0)" class="btn btn-danger remove_button remove_button_" ><i class="fa fa-trash" aria-hidden="true"></i></a>
          </div>`;

      let wrapper = $('.wrapper_' + (id - 1));
      $(wrapper).after(htmlTemplate);
      $(this).data('id', id);
      applyCloneMoneda();
      applyRemoveMoneda();
      applyTasaCambio();
      applyAll();
    });
  }

  applyTasaCambio = () => {
    $('.moneda_id').unbind('change').on('change', function() {
       var data_dolar = $("option:selected", this).data('dolar');
       var id = $(this).attr('id').split("moneda_id_")[1];
        if(data_dolar == 1) {
            $('#tasa_cambio_' + id).val('1,00').prop('readonly', true);
            $('#tasa_cambio_' + id).parent().addClass('hidden');
          } else {
            if($('#tasa_cambio_' + id).prop('readonly'))
              $('#tasa_cambio_' + id).val('');

            $('#tasa_cambio_' + id).prop('readonly', false);
            $('#tasa_cambio_' + id).parent().removeClass('hidden');
          }
    });
  }
</script>
@endsection
