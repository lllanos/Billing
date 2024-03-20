@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12">
        <ol class="breadcrumb">
            <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
            <li class="active">{!!trans('index.reporte_indices_valores')!!}</li>
        </ol>
        <div class="page-header">
            <h3 class="page_header__titulo titulo__fecha__front">
                <div class="titulo-header__input">
                    <div class="titulo__contenido"> @trans('index.reporte_indices_valores') @trans('index.en') <span id="moneda_title">{{$moneda->nombre_simbolo}}</span> </div>
                    <div class="titulo__input">
                        <select class="form-control chosen_title" name="select_anio" id="select_anio">
                            @foreach($anios as $keyAnio => $valueAnio)
                            <option value="{{ $keyAnio }}" @if($selected_anio==$valueAnio) selected @endif data-route="{{route('publicaciones.getHtmlTablareporteIndices', ['anio' => $valueAnio, 'moneda_id' => ':moneda_id'])}}" data-excelroute="{{ route('publicaciones.export.exportarIndices', ['anio' => $valueAnio, 'moneda_id' => ':moneda_id']) }}">
                                {{ $valueAnio }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="buttons-on-title titulo-header__button">
                    <div class="">
                        <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu pull-right multi-level" role="menu" aria-labelledby="dropdownMenu">
                                <li>
                                    <a href="{{ route('publicaciones.fuentesIndices') }}">{{trans('publicaciones.ver_fuentes')}}</a>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="levelToggle" tabindex="-1" href="#">
                                        @trans('index.seleccionar_moneda')
                                    </a>
                                    <ul class="dropdown-menu pull-right">
                                        @foreach($monedas as $keyMoneda => $valueMoneda)
                                        <li class="monedas_dd @if($valueMoneda->id == $moneda_id) hidden @endif" id="moneda_dd_{{$valueMoneda->id}}" data-id="{{$valueMoneda->id}}">
                                            <a href="#">
                                                {{$valueMoneda->nombre_simbolo}}
                                            </a>
                                        </li>
                                        @endforeach
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </h3>
        </div>
    </div>
    <input type="hidden" name="moneda_id" id="moneda_id" value="{{ $moneda_id }}" />
    <!--Input Excel-->
    <div class="input-group rounded col-xs-12 col-sm-offset-6 col-md-offset-6 col-sm-6 col-md-6 mb-1" role="group" aria-label="...">
        <div class="col-xs-12 col-sm-12 col-md-12 contenedor_input_dos_btns">

        <form class="form_excel" method="POST" data-action="{{ route('publicaciones.export.exportarIndices', ['anio' => $selected_anio, 'moneda_id' => $moneda_id]) }}" id="form_excel">
                {{ csrf_field() }}
                <input type="text" class="excel-search-input form-control" name="excel_input" id="excel_input" value="">
                <button type="submit" id="excel_button" class="btn btn-info btn-filter-toggle pull-left exportar btnGroupHeight btn_exc_izq" data-toggle="tooltip" data-placement="bottom" title="@trans('index.descargar_a_excel')}}" aria-label="@trans('index.descargar_a_excel')}}">
                    <i class="fa fa-file-excel-o fa-2x"></i>
                </button>
            </form>

            <input type="text" class="search-input form-control input_dos_btns" name="search_input_no_post" id="search_input_no_post" value="" aria-label="@trans('index.input') @trans('index.buscar')" placeholder="@trans('forms.busqueda_placeholder')">
            <span class="input-group-btn">
                <button type="submit" id="search_button_no_post" class="btn btn-info btn-filter-toggle" data-toggle="tooltip" data-placement="bottom" title="@trans('index.buscar')" aria-label="@trans('index.buscar')">
                    <i class="fa fa-search"></i>
                </button>
            </span>
        </div>
    </div>
    <!--Fin Input Excel-->

    <div class="col-md-12" id="panel_tabla">
        {!! $html_tabla !!}
    </div>


</div>
@endsection

@section('scripts')
$(document).ready(() => {
$('#select_anio').unbind('change').on('change', function() {
changeView();
});

$('.monedas_dd').off('click').on('click', function(e) {
$('#moneda_id').val($(this).data('id'));
changeView();
});
});

var changeView = () => {
loadingToggleThis('#panel_tabla');
var moneda_id = $('#moneda_id').val();
var route = $("#select_anio option:selected").data('route');
route = route.replace(':moneda_id', moneda_id);

var excelroute = $( "#select_anio option:selected" ).data('excelroute');
excelroute = excelroute.replace(':moneda_id', moneda_id);
$('#form_excel').data('action', excelroute);
$.get(route, function(data) {
loadingToggleThis('#panel_tabla');
$('#panel_tabla').html('').html(data.view);
$('#moneda_id').val(data.moneda_id);
$('.monedas_dd').removeClass('hidden');
$('#moneda_dd_' + data.moneda_id).addClass('hidden');
$('#moneda_title').text(data.moneda);
});
}

@endsection