@extends ('layout.app')

@section('title', config('app.name'))

@section('content')
<div class="row">
  <div class="col-md-12">
    <ol class="breadcrumb">
      <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
      <li><a href="{{route('contratistas.index')}}">{{trans('index.contratistas')}}</a></li>
      <li class="active">{!! trans('forms.detalle').' '.trans('forms.contratista') !!}</li>
    </ol>
    <div class="page-header">
      <h3 class="page_header__titulo">
        <div class="titulo__contenido">
          @trans('index.detalle') @trans('forms.contratista')
        </div>
        <div class="buttons-on-title">
          <div class="button_desktop">
            <a class="btn btn-success pull-right" href="{{route('contratistas.edit', $contratista->id) }}">
              @trans('index.editar') @trans('forms.contratista')
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
                <a href="{{route('contratistas.edit', $contratista->id) }}">
                  @trans('index.editar') @trans('forms.contratista')
                </a>
              </li>
            </ul>
          </div>
        </div>
      </h3>
    </div>

			<div class="panel panel-default">
			  <div class="panel-body container_detalle_contratista">
          <div class="row">
            <div class="col-md-6 col-sm-12">
              <label class="label__detalle__cont">{{trans('forms.tipo_contratista')}}</label>
              <span class="span__detalle__cont">{{ $contratista->tipo->nombre }}</span>
            </div>

            <div class="col-md-6 col-sm-12">
              <label class="label__detalle__cont">{{trans('forms.mail')}}</label>
              <span class="span__detalle__cont">{{ $contratista->email }}</span>
            </div>

            <div class="col-md-6 col-sm-12">
              <label class="label__detalle__cont">@trans('forms.name')</label>
              <span class="span__detalle__cont">{{ $contratista->razon_social }}</span>
            </div>

            <div class="col-md-6 col-sm-12">
              <label class="label__detalle__cont">{{trans('forms.tipo_doc_num_doc')}}</label>
              <span class="span__detalle__cont">{{ $contratista->tipo_num_documento }}</span>
            </div>

            @if($contratista->nombre_fantasia != null)
              <div class="col-md-6 col-sm-12">
                <label class="label__detalle__cont">{{trans('forms.nombre_fantasia')}}</label>
                <span class="span__detalle__cont">{{ $contratista->nombre_fantasia }}</span>
              </div>
            @endif

            @if($contratista->representante_legal != null)
              <div class="col-md-6 col-sm-12">
                <label class="label__detalle__cont">{{trans('forms.representante_legal')}}</label>
                <span class="span__detalle__cont">{{ $contratista->representante_legal }}</span>
              </div>
            @endif

            @if($contratista->entidad_bancaria != null)
              <div class="col-md-6 col-sm-12">
                <label class="label__detalle__cont">{{trans('forms.entidad_bancaria')}}</label>
                <span class="span__detalle__cont">{{ $contratista->entidad_bancaria }}</span>
              </div>
            @endif

            @if($contratista->cbu != null)
              <div class="col-md-6 col-sm-12">
                <label class="label__detalle__cont">{{trans('forms.cbu')}}</label>
                <span class="span__detalle__cont">{{ $contratista->cbu }}</span>
              </div>
            @endif

            @if($contratista->pais_id != null)
              <div class="col-md-6 col-sm-12">
                <label class="label__detalle__cont">{{trans('forms.pais')}}</label>
                <span class="span__detalle__cont">{{ $contratista->pais->nombre }}</span>
              </div>
            @endif

            @if($contratista->domicilio_legal != null)
              <div class="col-md-6 col-sm-12">
                <label class="label__detalle__cont">{{trans('forms.domicilio_legal')}}</label>
                <span class="span__detalle__cont">{{ $contratista->domicilio_legal }}</span>
              </div>
            @endif

            @if($contratista->observaciones != null)
              <div class="col-md-6 col-sm-12">
                <label class="label__detalle__cont">{{trans('forms.observaciones')}}</label>
                <span class="span__detalle__cont">{{ $contratista->observaciones }}</span>
              </div>
            @endif

            <div class="col-md-6 col-sm-12">
              <label class="label__detalle__cont">{{trans('forms.telefono')}}</label>
              <span class="span__detalle__cont">
                @foreach ($contratista->telefonos as $telefono)
                  <p>- {{ $telefono->prefijo_numero }}</p>
                @endforeach
              </span>
            </div>

          </div>
			  </div>

      @if(count($contratistasUTE) > 0)
        <div class="col-md-12 col-sm-12">
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
                      <tr>
                        <td>{{ $ct_ute->contratista->tipo->nombre }}</td>
                        <td>{{ $ct_ute->contratista->fantasia_razon_social }}</td>
                        <td>{{ $ct_ute->contratista->tipo_num_documento }}</td>
                        <td class="actions-col noFilter">
                          <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="{{ trans('index.acciones')}}">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="{{trans('index.acciones')}}">
                              <i class="fa fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu pull-right">
                              @permissions(('contrato-view'))
                                <li><a href="{{ route('contratistas.show', ['id'=> $ct_ute->contratista_id]) }}"><i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')</a></li>
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
      @endif

      @if(count($apareceUTE) > 0)
        <div class="col-md-12 col-sm-12">
          <div class="row list-table">
            <h3>{{trans('forms.ute_aparece')}}</h3>
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
                    @foreach($apareceUTE as $key => $ct_ute)
                      <tr>
                        <td>{{ $ct_ute->ute->tipo->nombre }}</td>
                        <td>{{ $ct_ute->ute->fantasia_razon_social }}</td>
                        <td>{{ $ct_ute->ute->tipo_num_documento }}</td>
                        <td class="actions-col noFilter">
                          <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="{{ trans('index.acciones')}}">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="{{trans('index.acciones')}}">
                              <i class="fa fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu pull-right">
                              @permissions(('contrato-view'))
                                <li><a href="{{ route('contratistas.show', ['id'=> $ct_ute->ute_id]) }}"><i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')</a></li>
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
      @endif

      <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
        <div class="text-right">
          <a class="btn btn-small btn-success" href="{{ route('contratistas.index') }}">@trans('forms.volver')</a>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection
