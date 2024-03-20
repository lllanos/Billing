@extends ('layout.app')

@section('title', config('app.name'))

@section('content')
<div class="row">
  <div class="col-md-12">
    <ol class="breadcrumb">
      <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
      <li><a href="{{route('contratistas.index')}}">{{trans('forms.contratistas')}}</a></li>
      <li class="active">{!! trans('forms.editar').' '.trans('forms.contratista') !!}</li>
    </ol>
    <div class="page-header">
      <h3 class="page_header__titulo">
        <div class="titulo__contenido">
          @trans('index.editar') @trans('forms.contratista')
        </div>
        <div class="buttons-on-title">
          <div class="button_desktop">
            <a class="btn btn-success pull-right" href="{{route('contratistas.show', $contratista->id) }}">
              @trans('index.ver') @trans('forms.contratista')
            </a>
          </div>
        </div>
        <div class="button_responsive">
          <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
            <button class="btn btn-primary dropdown-toggle" id="dd_menu" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
              <i class="fa fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu pull-right">
              <li>
                <a href="{{route('contratistas.show', $contratista->id) }}">
                  @trans('index.ver') @trans('forms.contratista')
                </a>
              </li>
            </ul>
          </div>
        </div> </h3>
    </div>

      @if (count($errors) > 0)
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="panel panel-default">
        <div class="panel-body container_detalle_contratista">
          {{ Form::open(array('url' => 'contratistas/update', 'class' => 'myForm')) }}
            <input type="hidden" name="borrador" id="borrador" value="{{ $contratista->borrador }}" />
            <input type="hidden" name="id" id="id" value="{{ $contratista->id }}" />

            <div class="row">
              <div class="col-md-6 col-sm-12">
                <div class="form-group">
                  <label class="label__detalle__cont">{{trans('forms.tipo_contratista')}}</label>
                  <span class="span__detalle__cont">{{ $contratista->tipo->nombre }}</span>
                </div>
              </div>
            </div>

            <div id="formulario">
              <div class="row">
                <div class="col-md-6 col-sm-12">
                  <div class="form-group">
                    {{ Form::label('tipo_documento_id', trans('forms.tipo_documento')) }}
                    {!! Form::select('tipo_documento_id', $tiposDocumentos, (null !== Input::old('tipo_documento_id')) ? Input::old('tipo_documento_id') : $contratista->documento_id, array('class' => 'form-control chosen-select', 'data-placeholder' => trans('forms.select.tipo_docuemnto'), 'id' => 'tipo_documento_id', 'required')) !!}
                  </div>
                </div>

                <div class="col-md-6 col-sm-12">
                  <div class="form-group">
                    {{ Form::label('nro_documento', trans('forms.nro_documento')) }}
                    {{ Form::text('nro_documento', (null !== Input::old('nro_documento')) ? Input::old('nro_documento') : $contratista->nro_documento, array('class' => 'form-control', 'required', 'autofocus', 'placeholder' => trans('forms.nro_documento'))) }}
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 col-sm-12">
                  <div class="form-group">
                    {{ Form::label('nombre', trans('forms.nombre_razon_social')) }}
                    {{ Form::text('nombre', (null !== Input::old('nombre')) ? Input::old('nombre') : $contratista->razon_social, array('class' => 'form-control', 'required', 'autofocus', 'placeholder' => trans('forms.nombre_razon_social'))) }}
                  </div>
                </div>

                <div class="col-md-6 col-sm-12">
                  <div class="form-group">
                    {{ Form::label('nombre_fantasia', trans('forms.nombre_fantasia')) }}
                    {{ Form::text('nombre_fantasia', (null !== Input::old('nombre_fantasia')) ? Input::old('nombre_fantasia') : $contratista->nombre_fantasia, array('class' => 'form-control', '', 'autofocus', 'placeholder' => trans('forms.nombre_fantasia'))) }}
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 col-sm-12">
                  <div class="form-group">
                    {{ Form::label('email', trans('forms.mail')) }}
                    {{ Form::email('email', (null !== Input::old('email')) ? Input::old('email') : $contratista->email, array('class' => 'form-control', 'required', 'placeholder' => trans('forms.mail'))) }}
                  </div>
                </div>

                <div class="col-md-6 col-sm-12">
                  <div class="form-group">
                    {{ Form::label('pais_id', trans('forms.pais')) }}
                    {!! Form::select('pais_id', $paises, (null !== Input::old('pais_id')) ? Input::old('pais_id') : $contratista->pais_id, array('class' => 'form-control chosen-select', 'data-placeholder' => trans('forms.select.pais'), 'id' => 'pais_id')) !!}
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 col-sm-12">
                  <div class="form-group">
                    {{ Form::label('domicilio_legal', trans('forms.domicilio_legal')) }}
                    {{ Form::text('domicilio_legal', (null !== Input::old('domicilio_legal')) ? Input::old('domicilio_legal') : $contratista->domicilio_legal, array('class' => 'form-control', 'required', 'placeholder' => trans('forms.domicilio_legal'))) }}
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 col-sm-12">
                  <div class="form-group">
                    {{ Form::label('entidad_bancaria', trans('forms.entidad_bancaria')) }}
                    {{ Form::text('entidad_bancaria', (null !== Input::old('entidad_bancaria')) ? Input::old('entidad_bancaria') : $contratista->entidad_bancaria, array('class' => 'form-control', '', 'autofocus', 'placeholder' => trans('forms.entidad_bancaria'))) }}
                  </div>
                </div>

                <div class="col-md-6 col-sm-12">
                  <div class="form-group">
                    {{ Form::label('cbu', trans('forms.cbu')) }}
                    {{ Form::text('cbu', (null !== Input::old('cbu')) ? Input::old('cbu') : $contratista->cbu, array('class' => 'form-control', '', 'autofocus', 'placeholder' => trans('forms.cbu'))) }}
                  </div>
                </div>
              </div>

              <div class="row">
                @if($contratista->tipo->nombre != 'Persona Física')
                  <div class="col-md-6 col-sm-12" id="div_representante_legal">
                    <div class="form-group">
                      {{ Form::label('representante_legal', trans('forms.representante_legal')) }}
                      {{ Form::text('representante_legal', (null !== Input::old('representante_legal')) ? Input::old('representante_legal') : $contratista->representante_legal, array('class' => 'form-control', 'required', 'autofocus', 'placeholder' => trans('forms.representante_legal'))) }}
                    </div>
                  </div>

                  <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                      {{ Form::label('observaciones', trans('forms.observaciones')) }}
                      {{ Form::textarea('observaciones', (null !== Input::old('observaciones')) ? Input::old('observaciones') : $contratista->observaciones, array('class' => 'form-control', '', 'autofocus', 'placeholder' => trans('forms.observaciones'))) }}
                    </div>
                  </div>
                @else
                  <div class="col-md-12 col-sm-12">
                    <div class="form-group">
                      {{ Form::label('observaciones', trans('forms.observaciones')) }}
                      {{ Form::textarea('observaciones', (null !== Input::old('observaciones')) ? Input::old('observaciones') : $contratista->observaciones, array('class' => 'form-control', '', 'autofocus', 'placeholder' => trans('forms.observaciones'))) }}
                    </div>
                  </div>
                @endif
              </div>

              <!-- BEGIN TELEFONO CLONE -->
              <div class="row container_telefonos">
                @ifcount($contratista->telefonos)
                  @foreach($contratista->telefonos as $tel)
                    <div class="col-md-12 col-sm-12 wrapper_{{$tel->id}} can-delete d-flex" id="telefono_{{$tel->id}}">
                      <div class="col-md-3 col-sm-12 pl-0">
                        <div class="form-group">
                          <label>{{trans('forms.telefono_prefijo')}}</label>
                          <input type='text' id='telefono_prefijo_[]' name='telefono_prefijo[]' class='form-control' value="{{$tel->prefijo}} ">
                        </div>
                      </div>
                      <input type="hidden" name="telefono_id[]" value="{{$tel->id}}">
                      <div class="col-md-8 col-sm-12 solo-ejecucion d-flex justify-content-cente align-items-center pr-0">
                        <div class="form-group col-md-12 p-0">
                          <label>{{trans('forms.telefono_numero')}}</label>
                          <input type='text' id='telefono_numero_[]' name='telefono_numero[]' class='form-control' value="{{$tel->numero}}">
                        </div>
                      </div>
                      <div class="col-md-1  col-sm-12  text-center">
                        <a class="mb-0 btn btn-danger btn-confirmable-prevalidado"
                          data-prevalidacion="{{ route('contratistas.preDeleteTelefono', ['id' => $tel->id]) }}"
                          data-body="{{trans('index.confirmar_eliminar.telefono')}}"
                          data-action="{{ route('contratistas.deleteTelefono', ['id' => $tel->id]) }}"
                          data-si="@trans('index.si')" data-no="@trans('index.no')">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </a>
                      </div>
                    </div>
                  @endforeach
                @elseifcount
                  <div class="col-md-12 col-sm-12 wrapper_1 d-flex p-0">
                    <div class="col-md-3 col-sm-12">
                      <div class="form-group">
                        <label>{{trans('forms.telefono_prefijo')}}</label>
                        <input type='text' id='telefono_prefijo_1' name='telefono_prefijo[1]' class='form-control' placeholder='{{trans('forms.telefono_prefijo')}}'>
                      </div>
                    </div>

                    <div class="col-md-9 col-sm-12 solo-ejecucion">
                      <div class="form-group">
                        <label>{{trans('forms.telefono_numero')}}</label>
                        <input type='text' id='telefono_numero_1' name='telefono_numero[1]' class='form-control' placeholder='{{trans('forms.telefono_numero')}}'>
                      </div>
                    </div>
                  </div>
                @endifcount
              </div>

              <div class="row">
                <div class="col justify-content-end">
                  <a href="javascript:void(0)" class="btn btn-primary pull-right add_button_tel" data-id="1">
                    {{trans('forms.agregar_telefono')}}<i class="fa fa-plus" aria-hidden="true"></i>
                  </a>

                </div>
              </div>
              <!-- END TELEFONO CLONE -->

              @if(count($contratistasUTE) > 0)
                <div class="col-md-12 col-sm-12 mb-3">
                  <div class="row list-table">
                    <h3>{{trans('forms.contratistas_integrantes')}}</h3>
                    <div class="zui-wrapper zui-action-32px-fixed">
                      <div class="zui-scroller"> <!-- zui-no-data -->
                        <table class="table table-striped table-hover table-bordered zui-table">
                          <thead>
                            <tr>
                              <th>{{trans('forms.tipo_contratista')}}</th>
                              <th>{{trans('forms.nombre')}}</th>
                              <th>{{trans('forms.tipo_doc_num_doc')}}</th>
                              <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($contratistasUTE as $key => $ct_ute)
                              <tr id="contratista_{{$ct_ute->contratista_id}}">
                                <td>{{ $ct_ute->contratista->tipo->nombre }}</td>
                                <td>{{ $ct_ute->contratista->fantasia_razon_social }}</td>
                                <td>{{ $ct_ute->contratista->tipo_num_documento }}</td>
                                <td class="actions-col noFilter">
                                  <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="{{ trans('index.acciones')}}">
                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="{{trans('index.acciones')}}">
                                      <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                      @permissions(('contratista-edit'))
                                        <li><a href="{{ route('contratistas.show', ['id'=> $ct_ute->contratista_id]) }}"><i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')</a></li>
                                        <li>
                                          <a class="eliminar btn-confirmable-prevalidado"
                                            data-prevalidacion="{{ route('contratistas.preDeleteContratista', ['id' => $ct_ute->contratista_id]) }}"
                                            data-body="{{trans('index.confirmar_eliminar.contratista', ['razon_social' => $ct_ute->contratista->fantasia_razon_social])}}"
                                            data-action="{{ route('contratistas.deleteContratista', ['id' => $ct_ute->contratista_id, 'uteId' => $ct_ute->ute_id] )}}"
                                            data-si="@trans('index.si')" data-no="@trans('index.no')">
                                              <i class="glyphicon glyphicon-remove"></i> @trans('index.eliminar')
                                          </a>
                                        </li>
                                      @endpermission
                                    </ul>
                                  </div>
                                </td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>

                <div id="ute">
                  <div class="row container_ute">
                    <!-- BEGIN INTEGRANTE CLONE -->
                    <div class="col-md-12 col-sm-12 wrapper_ute_1">
                      <div class="col-md-12 col-sm-12 m-0 p-0">
                        <div class="form-group m-0">
                          {{ Form::label('integrante_ute', trans('forms.integrante_ute')) }}
                          {!! Form::select('integrante_ute[1]', $contratistas_posibles, '', array('class' => 'form-control chosen-select', 'data-placeholder' => trans('forms.integrante_ute'), 'id' => 'integrante_ute_1')) !!}
                        </div>
                      </div>
                    </div>
                    <!-- END INTEGRANTE CLONE -->
                  </div>

                  <div class="row">
                    <div class="col justify-content-end">
                      <a href="javascript:void(0)" class="btn btn-primary pull-right add_button" data-id="1">
                        {{trans('forms.agregar_integrante')}}<i class="fa fa-plus" aria-hidden="true"></i>
                      </a>
                    </div>
                  </div>
                </div>
              @endif

              <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
                <div class="text-right">
                  {{ Form::button(trans('forms.guardar_borrador'), array('class' => 'btn btn-secondary btn-borrador hidden')) }}
                  <a class="btn btn-small btn-success" href="{{ url('contratistas') }}">@trans('forms.volver')</a>
                  {{ Form::button(trans('forms.guardar'), array('class' => 'btn btn-primary btn-guardar pull-right')) }}
                  {{ Form::submit(trans('forms.guardar'), array('class' => 'btn btn-primary pull-right hidden')) }}
                </div>
              </div>
            </div>
          {{ Form::close() }}
        </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')

  $(document).ready(() => {
    applyCloneTelefono();
    applyRemoveTelefono();

    applyCloneIntegrante();
    applyRemoveIntegrante();
    console.log()
  });

  $(function () {
    $('#tipo_id').on('change', function(evt, params) {
      if ($("#tipo_id").chosen().val() == "") {
        $("#formulario").css('visibility', 'hidden');
      } else {
        $("#formulario").css('visibility', 'visible');

        if ($("#tipo_id").chosen().val() == "1") {
          $('#div_representante_legal').hide();
          $('#representante_legal').removeAttr('required');
        } else {
          $('#div_representante_legal').show();
          $('#representante_legal').prop('required',true);
        }

        if ($("#tipo_id").chosen().val() == "3") {
          $("#ute").css('visibility', 'visible');
          $("#ute").css('display', 'block');
        } else {
          $("#ute").css('visibility', 'hidden');
          $("#ute").css('display', 'none');
        }
      }
    });

    $(".btn-borrador").click( function(event) {
      event.preventDefault();
      $('#borrador').val('1');
      $('#nombre').removeAttr('required');
      $('#email').removeAttr('required');
      $('#domicilio_legal').removeAttr('required');
      $('#representante_legal').removeAttr('required');
        var $myForm = $('.myForm');

      if(! $myForm[0].checkValidity()) {
        $myForm.find(':submit').click();
      } else {
         $myForm.find(':submit').click();
      }
    });


    $(".btn-guardar").click( function(event) {
      event.preventDefault();
      $('#borrador').val('0');
      $('#nombre').prop('required',true);
      $('#email').prop('required',true);
      $('#domicilio_legal').prop('required',true);
      if ($("#tipo_id").chosen().val() == "1") {
        $('#representante_legal').removeAttr('required');
      } else {
        $('#representante_legal').prop('required',true);
      }
        var $myForm = $('.myForm');

      if(! $myForm[0].checkValidity()) {
        $myForm.find(':submit').click();
      } else {
         $myForm.find(':submit').click();
      }
    });
  });

  applyRemoveTelefono = () => {
    $('.remove_button').off('click').on('click', function(e) {
      e.preventDefault();
      $(this).parents('div.can-delete').remove();
      applyCloneTelefono();
    });
  };

  applyCloneTelefono = () => {
    $('.add_button_tel').unbind('click').click(function() {
      var id = $(this).data('id') + 1;
      console.log(id)

      if($(this).parents('div.clone-container').hasClass('can-delete'))
        $(this).parent().find('.remove_button').removeClass('hidden');

      var hidden = " ";
      if($('select[name="estado_id"').find(':selected').data('ejecucion') == 0) {
        var hidden = " hidden";
      }

      const htmlTemplate = `
          <div class="col-md-12 col-sm-12 wrapper_${id} can-delete d-flex">
            <div class="col-md-3 col-sm-12 pl-0">
              <div class="form-group">
                <label>{{trans('forms.telefono_prefijo')}}</label>
                <input type='text' id='telefono_prefijo_1${id}' name='telefono_prefijo[${id}]' class='form-control' placeholder='{{trans('forms.telefono_prefijo')}}'>
              </div>
            </div>

            <div class="col-md-8 col-sm-12 solo-ejecucion ${hidden} d-flex justify-content-cente align-items-center pr-0">
              <div class="form-group col-md-12 p-0">
                <label>{{trans('forms.telefono_numero')}}</label>
                <input type='text' id='telefono_numero_1${id}' name='telefono_numero[${id}]' class='form-control' placeholder='{{trans('forms.telefono_numero')}}'>
              </div>
            </div>
             <div class="col-md-1  col-sm-12  text-center">
              <a href="javascript:void(0)" class="btn btn-danger remove_button m-0"><i class="fa fa-trash" aria-hidden="true"></i></a>
             </div>
      </div>`;

      let wrapper = $('.container_telefonos');
      $(wrapper).append(htmlTemplate);
      $(this).data('id', id);
      applyCloneTelefono();
      applyRemoveTelefono();

      applyAll();
    });
  }

  applyRemoveIntegrante = () => {
    $('.remove_button').off('click').on('click', function(e) {
      e.preventDefault();
      $(this).parents('div.can-delete').remove();
      applyCloneIntegrante();
    });
  };

  applyCloneIntegrante = () => {
    $('.add_button').unbind('click').click(function() {
      var id = $(this).data('id') + 1;

      if($(this).parents('div.clone-container').hasClass('can-delete'))
        $(this).parent().find('.remove_button').removeClass('hidden');

      var hidden = " ";
      if($('select[name="estado_id"').find(':selected').data('ejecucion') == 0) {
        var hidden = " hidden";
      }

      const htmlTemplate = `
                    <div class="col-md-12 col-sm-12 wrapper_ute_${id} can-delete">
                      <div class="col-md-12 col-sm-12 p-0 d-flex">
                        <div class="form-group col-md-12 p-0 m-0">
                            {{ Form::label('integrante_ute', trans('forms.integrante_ute')) }}
                            {!! Form::select('integrante_ute[${id}]', $contratistas_posibles, '', array('class' => 'form-control chosen-select', 'data-placeholder' => trans('forms.integrante_ute'), 'id' => 'integrante_ute_1${id}')) !!}
                        </div>
                        <a href="javascript:void(0)" class="btn btn-danger remove_button m-0"><i class="fa fa-trash" aria-hidden="true"></i></a>
                      </div>
                    </div>`;

      let wrapper = $('.container_ute');
      $(wrapper).append(htmlTemplate);
      $(this).data('id', id);
      applyCloneIntegrante();
      applyRemoveIntegrante();

      applyAll();
    });
  }
@endsection
