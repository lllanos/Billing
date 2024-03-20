@extends ('layout.app')

@section('title', config('app.name'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <ol class="breadcrumb">
                <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
                <li><a href="{{route('publicaciones.index')}}">{{trans('forms.publicaciones')}}</a></li>
                <li class="active">{!!trans('index.indices_mensual')!!}</li>
            </ol>

            <div class="page-header page_header__badge">
                <h3 class="page_header__titulo">
                    <div class="titulo__contenido">
                        {!!trans('index.indices_mensual')!!} {{ $publicacion->mes_anio }}

                        <span class="badge" style="background-color:{{ '#' . $publicacion->estado_nombre_color['color'] }};">
                            {{ $publicacion->estado_nombre_color['nombre'] }}
                        </span>
                    </div>

                    <div class="buttons-on-title">
                        <div class="button_desktop">
                            @permissions('publicacion-ver_historial')
                            <a class="btn btn-primary open-historial mouse-pointer"
                               data-url="{{ route('publicaciones.historial', ['id' => $publicacion->id]) }}"
                               id="btn_historial" data-toggle="tooltip" data-placement="bottom"
                               title="@trans('index.historial')">
                                <i class="fa fa-history" aria-hidden="true"></i>
                            </a>
                            @endpermission

                            @permissions('publicacion-edit')
                            @if(!$publicacion->publicado && !$publicacion->hay_publicaciones_publicadas_siguientes)
                                <a class="btn btn-primary submit"
                                   href="{{route('publicaciones.edit', ['id' => $publicacion->id])}}" id="btn_editar"
                                   data-accion="btn_editar">
                                    {{trans('forms.editar')}}
                                </a>
                            @endif
                            @endpermission
                        </div>

                        <div class="button_responsive">
                            <div class="dropdown dd-on-table" data-placement="left">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                    <i class="fa fa-ellipsis-v"></i>
                                </button>

                                <ul class="dropdown-menu pull-right">
                                    @permissions(('publicacion-editar'))
                                    <li>
                                        <a class="submit" id="btn_{{$valueAccion}}_resp" data-accion="{{$valueAccion}}">
                                            {{trans('forms.editar')}}
                                        </a>
                                    </li>
                                    @endpermission
                                </ul>
                            </div>
                        </div>
                    </div>
                </h3>
            </div>
        </div>

        <!--Input file excel con 1 form-->
        <div class="input-group rounded col-xs-12 col-sm-12 col-md-12 mb-_5 badges_vr__input_excel" role="group"
             aria-label="...">
            <div class="col-xs-12 col-sm-6 col-md-6 container_badges_vr container_badges_vr_indices">
                <span class="">{{trans('index.referencias')}}</span>
                <label class="label indice_no_se_publica">{{trans('index.no_publica')}}</label>
            </div>

            <div class="col-xs-12 col-sm-6 col-md-6 contenedor_input_dos_btns">
                <form class="form_excel" method="POST" data-action="{{ route('publicaciones.export.edit', ['id' => $publicacion->id]) }}" id="form_excel">
                    {{ csrf_field() }}

                    <input type="text" class="excel-search-input form-control" name="excel_input" id="excel_input" value="">

                    <button type="submit" id="excel_button"
                        class="btn btn-info btn-filter-toggle pull-left exportar btnGroupHeight btn_exc_izq"
                        data-toggle="tooltip" data-placement="bottom" title="@trans('index.descargar_a_excel')"
                        aria-label="@trans('index.descargar_a_excel')"
                    >
                        <i class="fa fa-file-excel-o fa-2x"></i>
                    </button>
                </form>

                <!--btn_excel-->
                <input type="text"
                    class="search-input form-control input_dos_btns"
                    name="search_input_no_post"
                    id="search_input_no_post"
                    value=""
                    aria-label="{{trans('index.input')}} @trans('index.buscar')"
                    placeholder="@trans('forms.busqueda_placeholder')"
                />

                <span class="input-group-btn">
                    <button
                        type="submit"
                        id="search_button_no_post"
                        class="btn btn-info btn-filter-toggle"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="@trans('index.buscar')"
                        aria-label="@trans('index.buscar')"
                    >
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        </div>

        <!-- Input file excel con 1 form-->
        <div class="col-md-12">
            <div class="errores-publicacion hidden alert alert-danger"></div>
        </div>

        <div class="col-md-12">
            <form method="POST" data-action="{{route('publicaciones.update', ['id' => $publicacion->id ])}}"
                  id="form-publicaciones">
                {{ csrf_field() }}
                <input class="hidden" name="accion" id="accion">
                <!--Panel-->
                <div class="">
                    <div class="panel-body panel_con_tablas_y_sub_tablas p-0 contenedor_all_tablas">
                        @php($contador_categoria = 1)

                        @php($contador_sub_categoria = 1)

                        @if(sizeof($valores_por_categoria) > 0)
                            @foreach($valores_por_categoria as $keyMoneda => $valueMoneda)
                                <h5>{{$valueMoneda['moneda']}}</h5>

                                @foreach($valueMoneda['valores'] as $keyCategoria => $categoria)
                                <!--Collapse-->
                                    <div
                                        class="panel-group colapsable_top moneda-{{$valueMoneda['moneda_key']}}"
                                        id="accordion_{{$contador_categoria}}"
                                        role="tablist"
                                        aria-multiselectable="true"
                                    >
                                        <div class="panel panel-default">
                                            <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse"
                                                 role="tab"
                                                 id="headingOne_{{$contador_categoria}}"
                                            >
                                                <h4 class="panel-title m-0 titulo_collapse">
                                                    <a class="collapse_arrow"
                                                       role="button"
                                                       data-toggle="collapse"
                                                       data-parent="#accordion_{{$contador_categoria}}"
                                                       href="#collpapse_{{$contador_categoria}}"
                                                       aria-expanded="true"
                                                       aria-controls="collpapse_{{$contador_categoria}}"
                                                    >
                                                        <i class="fa fa-angle-down"></i> {{$keyCategoria}}
                                                    </a>
                                                </h4>
                                            </div>

                                            <div id="collpapse_{{$contador_categoria}}"
                                                 class="panel-collapse collapse in"
                                                 role="tabpanel"
                                                 aria-labelledby="headingOne_{{$contador_categoria}}"
                                            >
                                                <div class="panel-body panel_sub_tablas pl-1 pt-1 pr-1 pb-0">
                                                @foreach($categoria as $keySubcategoria => $subcategoria)
                                                    @if($keySubcategoria != 'N/A')
                                                        <!--Sub Collpase-->
                                                            <div class="panel-group colapsable_sub"
                                                                 id="accordion_sub_{{$contador_sub_categoria}}"
                                                                 role="tablist" aria-multiselectable="true">
                                                                <div class="panel panel-default">
                                                                    <div
                                                                        class="panel-heading panel_heading_collapse p-0"
                                                                        role="tab"
                                                                        id="headingOne_sub_{{$contador_sub_categoria}}">
                                                                        <h4 class="panel-title pl-2 m-0 titulo_collapse">
                                                                            <a class="collapse_arrow" role="button"
                                                                               data-toggle="collapse"
                                                                               data-parent="#accordion_sub_{{$contador_sub_categoria}}"
                                                                               href="#collapseOne_sub{{$contador_sub_categoria}}"
                                                                               aria-expanded="true"
                                                                               aria-controls="collapseOne_sub{{$contador_sub_categoria}}">
                                                                                <i class="fa fa-angle-down"></i> {{$keySubcategoria}}
                                                                            </a>
                                                                        </h4>
                                                                    </div>
                                                                    <div id="collapseOne_sub{{$contador_sub_categoria}}"
                                                                         class="panel-collapse collapse in"
                                                                         role="tabpanel"
                                                                         aria-labelledby="headingOne_sub_{{$contador_categoria}}">
                                                                        @endif
                                                                        <div
                                                                            class="panel-body panel_con_tablas_y_sub_tablas p-0">
                                                                            <!--Tabla scrollable-->
                                                                            <div class="col-md-12 col-sm-12 pt-0 pb-1">
                                                                                <div class="list-table p-0">
                                                                                    <div
                                                                                        class="zui-wrapper zui-action-32px-fixed">
                                                                                        <div
                                                                                            class="zui-scroller zui-no-data">
                                                                                            <!-- zui-no-data -->
                                                                                            <table
                                                                                                class="table table-striped table-hover zui-table">
                                                                                                <thead>
                                                                                                <tr>
                                                                                                    <th class="text-center tb_indice_nro">
                                                                                                        #
                                                                                                    </th>
                                                                                                    <th class="tb_indice_nombre">@trans('forms.nombre')</th>
                                                                                                    <th class="tb_indice_fuente">@trans('forms.fuente')</th>
                                                                                                    <th class=" tb_valor_anterior">@trans('forms.valor_anterior')</th>
                                                                                                    <th class="tb_nvo_valor">@trans('forms.nuevo_valor')</th>
                                                                                                    <th class="tb_indice_vr text-center">@trans('forms.vr')</th>
                                                                                                </tr>
                                                                                                </thead>
                                                                                                <tbody
                                                                                                    class="tbody_con_input tbody_tooltip">
                                                                                                @foreach($subcategoria as $keyIndice => $valor_indice)
                                                                                                    <tr class="@if($valor_indice->indice_tabla1->no_se_publica) indice_no_se_publica @endif" style="@if($valor_indice->valor_show == 0)background-color: #f54848;@endif">
                                                                                                        <td class="text-center tb_indice_nro">
                                                                                                            @if($valor_indice->indice_tabla1->compuesto)
                                                                                                                <span
                                                                                                                    data-toggle="tooltip"
                                                                                                                    data-html="true"
                                                                                                                    data-placement="bottom"
                                                                                                                    title="{{$valor_indice->indice_tabla1->mensaje_composicion}}">
                                                          <i class="fa fa-tasks" aria-hidden="true"></i>
                                                      @else
                                                                                                                        <span>
                                                      @endif
                                                                                                                            {{$valor_indice->indice_tabla1->nro}}
                                                        </span>
                                                                                                        </td>
                                                                                                        <td class="tb_indice_nombre"
                                                                                                            title="{{$valor_indice->indice_tabla1->nombre}}">
                                                      <span data-toggle="tooltip" data-placement="bottom"
                                                            title="{{$valor_indice->indice_tabla1->nombre}}">
                                                        {{$valor_indice->indice_tabla1->nombre}}
                                                      </span>
                                                                                                            <span
                                                                                                                class="hidden">{{$valueMoneda['moneda']}}</span>
                                                                                                        </td>
                                                                                                        <td class="tb_indice_fuente">
                                                                                                            @if($valor_indice->indice_tabla1->fuente_id != null)
                                                                                                                <span
                                                                                                                    data-toggle="tooltip"
                                                                                                                    data-placement="bottom"
                                                                                                                    title="{{$valor_indice->indice_tabla1->fuente->nombre}}">
                                                          {{$valor_indice->indice_tabla1->fuente->nombre}}
                                                        </span>
                                                                                                            @endif
                                                                                                        </td>
                                                                                                        <td class="text-right tb_valor_anterior">
                                                                                                            <span
                                                                                                                id="valor_old_{{$valor_indice->tabla1_id}}">{{$valor_indice->valor_anterior_show}}</span>
                                                                                                        </td>
                                                                                                        <td class="text-right tb_nvo_valor">{{$valor_indice->valor_show}}</td>
                                                                                                        <td class="tb_indice_vr text-center">
                                                                                                            <label
                                                                                                                id="vr_{{$valor_indice->tabla1_id}}"
                                                                                                                class="label label_default {{$valor_indice->color_class}} text-center">
                                                                                                                {{$valor_indice->variacion_show}}
                                                                                                            </label>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                @endforeach
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <!--Fin Tabla scrollable-->
                                                                        </div>
                                                                        @if($keySubcategoria != 'N/A')
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @php($contador_sub_categoria++)
                                                    @endforeach
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <!--FIN Collapse-->
                                    @php($contador_categoria++)
                                @endforeach
                            @endforeach

                    </div>
                    @else
                        <div class="sin_datos">
                            <h1 class="text-center">@trans('index.no_datos')</h1>
                        </div>
                    @endif
                    <div class="sin_datos_js"></div>
                </div>
                <!--Fin Panel-->
            </form>
        </div>
    </div>
@endsection

@section('modals')
    @include('publicaciones.modals')
@endsection

@section('scripts')

    $(document).ready(() => {
    applyModalHistorial();
    });
@endsection
