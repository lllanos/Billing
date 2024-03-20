@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li class="active">@trans('index.mis') @trans('index.solicitudes_redeterminacion')</li>
      </ol>
      <div class="page-header">
        <h3 class="page_header__titulo">
          <div class="titulo__contenido">
            @trans('index.mis') @trans('index.solicitudes_redeterminacion')
          </div>
          <div class="buttons-on-title">
            <div class="button_desktop">
              <a class="btn btn-success" href="{{ route('solicitudes.redeterminaciones.solicitar') }}" id="btn_solicitar_redeterminacion">
                @trans('forms.solicitar_nueva') @trans('index.redeterminacion')
              </a>
            </div>
            <div class="button_responsive">
              <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                  <i class="fa fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu pull-right">
                  <li>
                    <a href="{{ route('solicitudes.redeterminaciones.solicitar') }}">
                      @trans('forms.solicitar_nueva') @trans('index.redeterminacion')
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </h3>
      </div>
    </div>

    <!--Input file excel con 1 form-->
    <div class="input-group rounded col-xs-12 col-sm-12 col-md-12 mb-_5" role="group" aria-label="...">
      <div class="col-xs-12 col-sm-offset-6 col-sm-6 col-md-offset-6 col-md-6 contenedor_input_dos_btns mb-_5">
        <form  class="form_excel" method="POST" data-action="{{ route('solicitudes.redeterminacion.export') }}" id="form_excel">
          {{ csrf_field() }}
          <input type="text" class="excel-search-input form-control" name="excel_input" id="excel_input">
          <button type="submit" id="excel_button" class="btn btn-info btn-filter-toggle pull-left exportar btnGroupHeight btn_exc_izq" data-toggle="tooltip" data-placement="bottom" title="@trans('index.descargar_a_excel')}}" aria-label="@trans('index.descargar_a_excel')}}">
            <i class="fa fa-file-excel-o fa-2x"></i>
          </button>
        </form>
        <form method="POST" data-action="{{ route('redeterminaciones.index.post') }}" id="search_form">
          {{ csrf_field() }}
          <input type="text" class="search-input form-control input_dos_btns enter_submit buscar_si" name="search_input" id="search_input" value ="{{$search_input}}" placeholder="@trans('forms.busqueda_placeholder')">
          <span class="input-group-btn">
            <button type="submit" id="search_button" class="btn btn-info btn-filter-toggle" data-toggle="tooltip" data-placement="bottom" title="@trans('index.buscar')" aria-label="@trans('index.buscar')">
              <i class="fa fa-search"></i>
            </button>
          </span>
        </form>
      </div>
    </div>
    <!--Fin Input file excel con 1 form-->

    @if(sizeof($solicitudes) > 0)
    <div class="col-md-12 col-sm-12">
      <div class="list-table p-0">
        <div class="zui-wrapper zui-action-32px-fixed">
          <div class="zui-scroller"> <!-- zui-no-data -->
            <table class="table table-striped table-hover table-bordered zui-table">
              <thead>
                <tr>
                  <th class="text-center"><!-- Columa "a termino" --></th>
                  <th>@trans('forms.expediente_madre')</th>
                  <th>@trans('forms.description')</th>
                  <th>@trans('forms.fecha_solicitud_th')</th>
                  <th>@trans('forms.salto')</th>
                  <th>@trans('forms.estado')</th>
                  <th>@trans('forms.ultimo_movimiento_th')</th>
                  <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                </tr>
              </thead>
              <tbody class="tbody_js">
                @foreach ($solicitudes as $key => $valueSolicitud)
                  <tr>
                    <td class="text-center">
                      @if($valueSolicitud->a_termino)
                        <span>
                          <i class="fa fa-check-circle text-success"
                           data-toggle="tooltip" data-placement="top" title="@trans('sol_redeterminaciones.a_termino')"></i>
                        </span>
                      @else
                        <span>
                          <i class="fa fa-times-circle text-danger"
                           data-toggle="tooltip" data-placement="top" title="@trans('sol_redeterminaciones.no_a_termino')"></i>
                        </span>
                      @endif
                    </td>
                    <td>{{ $valueSolicitud->salto->contrato_moneda->contrato->expediente_madre }}</td>
                    <td>{{ $valueSolicitud->user_contrato->descripcion }}</td>
                    <td>{{ $valueSolicitud->created_at }}</td>
                    <td>
                      <span class="badge" style="background-color:var(--green-redeterminacion-color);">
                        {{ $valueSolicitud->salto->moneda_mes_anio }}
                      </span>
                    </td>
                    <td>
                      <div class="contenedor_badges_estado_contrato">
                        @if($valueSolicitud->en_curso)
                          <span class="m-0 badge badge-referencias badge_esperando">@trans('index.esperando')</span>
                        @endif
                        <span class="m-0 badge badge-referencias container_estado_redeterminacion @if($valueSolicitud->en_curso) badge_esperando_estado @endif" style="background: #{{$valueSolicitud->estado_nombre_color['color']}}" >
                          <span>{{$valueSolicitud->estado_nombre_color['nombre']}}</span>
                        </span>
                      </div>
                    </td>
                    <td>{{ $valueSolicitud->ultimo_movimiento }}</td>
                    <td class="actions-col noFilter">
                      <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                          <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu pull-right">
                          <li><a href="{{ route('solicitudes.ver', ['id' => $valueSolicitud->id]) }}"><i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')</a></li>
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
      {{ $solicitudes->render() }}
    </div>
    @else
      <div class="col-md-12 col-sm-12">
        <div class="sin_datos_js"></div>
        <div class="sin_datos">
          <h1 class="text-center">@trans('index.no_datos')</h1>
        </div>
      </div>
    @endif
  </div>
@endsection
