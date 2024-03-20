@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li class="active">@trans('index.list_of') @trans('forms.alarma_solicitud')</li>
      </ol>
      <div class="page-header">
        <h3 class="page_header__titulo">
          <div class="titulo__contenido">
            @trans('index.list_of') @trans('forms.alarma_solicitud')
          </div>
          <div class="buttons-on-title">
            @permission(('alarma-create'))
              <div class="button_desktop">
                <a class="btn btn-success pull-right" href="{{ route('alarmas.solicitud.create') }}">
                  @trans('index.nueva') @trans('index.alarma')
                </a>
              </div>
              <div class="button_responsive">
                <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
                  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                    <i class="fa fa-ellipsis-v"></i>
                  </button>
                  <ul class="dropdown-menu pull-right">
                    <li>
                      <a class="btn btn-success pull-right" href="{{ route('alarmas.solicitud.create') }}">
                        @trans('index.nueva') @trans('index.alarma')
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            @endpermission
          </div>
        </h3>
      </div>
    </div>

    @if(sizeof($alarmas) > 0)
    <div class="col-md-12 col-sm-12">
      <div class="list-table">
        <div class="zui-wrapper zui-action-32px-fixed">
          <div class="zui-scroller"> <!-- zui-no-data -->
            <table class="table table-striped table-hover table-bordered zui-table">
              <thead>
                <tr>
                  <th></th>
                  <th>@trans('forms.destinatario')</th>
                  <th>@trans('forms.name')</th>
                  <th>@trans('forms.desencadenante')</th>
                  <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                </tr>
              </thead>
              <tbody>
                @foreach($alarmas as $keyAlarma => $valueAlarma)
                  <tr id="contratista_{{$valueAlarma->id}}">
                    <td class="text-center">
                      @if($valueAlarma->habilitada)
                        <i class="fa fa-check-circle text-success" data-toggle="tooltip" data-placement="top" title="@trans('index.habilitada')"></i>
                      @else
                        <i class="fa fa-times-circle text-danger" data-toggle="tooltip" data-placement="top" title="@trans('index.no_habilitada')"></i>
                      @endif
                    </td>
                    <td> @if($valueAlarma->usuario_sistema) @trans('index.eby') @else @trans('forms.contratista') @endif</td>
                    <td>{{ $valueAlarma->nombre }}</td>
                    <td>
                      <span class="badge m-0" style="background: #{{$valueAlarma->desencadenante->color}}" >
                        <span>
                          {{-- @if($valueAlarma->correccion)
                            @trans('index.corregir') @trans('redeterminaciones.corregir.' . $valueAlarma->desencadenante->modelo)
                          @else --}}
                            @trans('sol_redeterminaciones.acciones.' . $valueAlarma->desencadenante->modelo)
                          {{-- @endif --}}
                        </span>
                      </span>
                    </td>
                    <td class="actions-col noFilter">
                      <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.acciones')">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.acciones')">
                          <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu pull-right">
                          @permission(('alarma-create'))
                            <li>
                              <a href="{{ route('alarmas.solicitud.show', ['id' => $valueAlarma->id]) }}">
                                <i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')
                              </a>
                            </li>
                            <li>
                              <a href="{{ route('alarmas.solicitud.edit', ['id' => $valueAlarma->id]) }}">
                                <i class="glyphicon glyphicon-pencil"></i> @trans('index.editar')
                              </a>
                            </li>
                          @endpermission
                          @permission(('alarma-habilitar'))
                            <li>
                              @if($valueAlarma->habilitada)
                                <a data-toggle="tooltip" data-placement="left" title="@trans('index.deshabilitar')" class="btn-confirmable"
                                 data-body="@trans('index.confirmar_habilitar.alarma_disable', ['nombre' => $valueAlarma->nombre]) "
                                  data-action="{{ route('alarmas.solicitud.deshabilitar', ['id' => $valueAlarma->id]) }}"
                                  data-si="@trans('index.si')" data-no="@trans('index.no')">
                                  <i class="glyphicon glyphicon-remove"></i> @trans('index.deshabilitar')
                                </a>
                                @else
                                  <a data-toggle="tooltip" data-placement="left" title=" @trans('index.habilitar')" class="btn-confirmable"
                                   data-body="@trans('index.confirmar_habilitar.alarma_enable', ['nombre' => $valueAlarma->nombre]) "
                                    data-action="{{ route('alarmas.solicitud.habilitar', ['id' => $valueAlarma->id]) }}"
                                    data-si="@trans('index.si')" data-no="@trans('index.no')">
                                    <i class="glyphicon glyphicon-ok"></i> @trans('index.habilitar')
                                  </a>
                                @endif
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
        {{ $alarmas->render() }}
      </div>
    </div>
    @else
      <div class="col-md-12 col-sm-12">
        <div class="sin_datos">
          <h1 class="text-center">@trans('index.no_datos')</h1>
        </div>
      </div>
    @endif
  </div>
@endsection
