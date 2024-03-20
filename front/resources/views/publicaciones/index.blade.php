@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12">
        <ol class="breadcrumb">
            <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
            <li class="active">{!!trans('index.list_of')!!} {!!trans('forms.publicaciones')!!}</li>
        </ol>
        <div class="page-header">
            <h3 class="page_header__titulo">
                <div class="titulo__contenido">
                    {!!trans('index.list_of')!!} {!!trans('forms.publicaciones')!!}
                </div>
            </h3>
        </div>
    </div>

    <!--Input file excel con 2 form-->
    <div class="input-group rounded col-xs-12 col-sm-12 col-md-12 mb-_5" role="group" aria-label="...">
        <div class="col-xs-12 col-sm-offset-6 col-sm-6 col-md-offset-6 col-md-6 contenedor_input_dos_btns mb-_5">
            @permissions(('publicacion-export'))
            <form class="form_excel" method="POST" data-action="{{ route('publicaciones.export') }}" id="form_excel">
                {{ csrf_field() }}
                <input type="text" class="excel-search-input form-control" name="excel_input" id="excel_input" value="{{$search_input}}" placeholder="@trans('forms.busqueda_placeholder')">
                <button type="submit" id="excel_button" class="btn btn-info btn-filter-toggle pull-left exportar btnGroupHeight btn_exc_izq" data-toggle="tooltip" data-placement="bottom" title="@trans('index.descargar_a_excel')" aria-label="@trans('index.descargar_a_excel')">
                    <i class="fa fa-file-excel-o fa-2x"></i>
                </button>
            </form>
            @endpermission
            <form method="POST" data-action="{{ route('publicaciones.index.post') }}" id="search_form">
                <button type="button" style="padding: 12px 19px !important" class="btn btn-default dropdown-toggle btnGroupHeight" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-dollar"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li>
                        <a href="{{ route('publicaciones.index') }}">
                            Todas
                        </a>
                    </li>
                    @foreach($monedas as $keyMoneda => $valueMoneda)
                    <li>
                        <a href="{{route('publicaciones.filtrarPorMoneda', ['id' => $valueMoneda->id]) }}">
                            {{ $valueMoneda["nombre"] }}
                        </a>
                    </li>
                    @endforeach
                </ul>
                {{ csrf_field() }}
                <div class="input-group" style="display:inline;">
                    <input type="text" class="search-input form-control input_dos_btns buscar_si" name="search_input" id="search_input" value="{{$search_input}}" placeholder="@trans('forms.busqueda_placeholder')" style="width: calc(100% - 160px) !important;">
                    <span class="input-group-btn">
                        <button type="submit" id="search_button" class="btn btn-info btn-filter-toggle buscar-si" data-toggle="tooltip" data-placement="bottom" title="@trans('index.buscar')" aria-label="@trans('index.buscar')">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
            </form>
        </div>
    </div>
    <!--Fin Input file excel con 2 form-->
    @if(sizeof($publicaciones) > 0)
    <div class="col-md-12 col-sm-12">
        <div class="list-table pt-0">
            <div class="zui-wrapper zui-action-32px-fixed">
                <div class="zui-scroller zui-no-data">
                    <!-- zui-no-data -->
                    <table class="table table-striped table-hover zui-table">
                        <thead>
                            <tr>
                                <th>{{trans('forms.mes_indice')}}</th>
                                <th>{{trans('forms.moneda')}}</th>
                                <th>{{trans('forms.fecha_publicacion')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($publicaciones as $keyPublicacion => $valuePublicacion)
                            <tr id="contrato_{{$valuePublicacion->id}}">
                                <td>{{ $valuePublicacion->mes_anio }}</td>
                                <td>
                                    {{ $valuePublicacion->moneda['nombre'] }}
                                </td>
                                <td>{{ $valuePublicacion->fecha_publicacion }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{ $publicaciones->render() }}
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