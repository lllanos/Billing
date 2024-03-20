@extends ('layout.app')

@section('title', config('app.name'))

@section('content')
<div class="row">
  <div class="col-md-12">
    <ol class="breadcrumb">
      <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
      <li><a href="{{route('alarmas.solicitud')}}">@trans('index.alarmas')</a></li>
      <li class="active">@trans('index.ver') @trans('forms.alarma_solicitud')</li>
    </ol>
    <div class="page-header">
      <h3 class="page_header__titulo">
        <div class="titulo__contenido">
          @trans('index.ver') @trans('forms.alarma_solicitud')
        </div>
        <div class="buttons-on-title">
          <div class="button_desktop">
            <a class="btn btn-success pull-right" href="{{route('alarmas.solicitud.edit', ['id' => $alarma->id]) }}">
              @trans('index.editar') @trans('forms.alarma_solicitud')
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
                <a href="{{route('alarmas.solicitud.edit', ['id' => $alarma->id]) }}">
                  @trans('index.editar') @trans('forms.alarma_solicitud')
                </a>
              </li>
            </ul>
          </div>
        </div>
      </h3>
    </div>

    <div class="panel panel-default">
      <div class="panel-body">

        <input name="tipo_desencadenante_id" id="tipo_desencadenante_id" class="hidden" value="1">

        <div class="row">
          <div class="col-md-6 col-sm-12 mb-2">
            <span class="span_title">
              @trans('forms.nombre'):
            </span>
            <span class="span_dato">
              {{$alarma->nombre}}
            </span>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 col-sm-12 mb-2">
            <span class="span_title">
              @trans('index.tipo_usuario'):
            </span>
            @if(!$alarma->usuario_sistema)
              <span class="span_dato">
                @trans('forms.contratista')
              </span>
            @else
              <span class="span_dato">
                @trans('index.eby')
              </span>
            @endif
          </div>

          <!-- Contratista -->
          @if(!$alarma->usuario_sistema)
            <div class="col-md-12 col-sm-12 toggleHidden">
              @trans('index.usuario_contrato')
            </div>
          @else
          <!--Fin contratista-->

          <!-- DNV -->
          <div class="col-md-12 col-sm-12 toggleHidden">
            <div class="col-md-6 col-sm-12">
              <span class="span_title">
                @trans('forms.rol'):
              </span>
              <span class="span_dato">
                {{$alarma->role->name}}
              </span>
            </div>
            <div class="col-md-6 col-sm-12">
              <div class="form-group">
                <span class="span_title">
                  @trans('forms.causante'):
                </span>
                @if($alarma->causante_id == null && $alarma->responsable_contrato)
                  <span class="span_dato">
                    @trans('forms.causante_responsable')
                  </span>
                @elseif($alarma->causante != null)
                  <span class="span_dato">
                    {{$alarma->causante->nombre}}
                  </span>
                @else
                  <span class="span_dato">
                    @trans('forms.todos.causante')
                  </span>
                @endif
              </div>
            </div>
          </div>
          <!--Fin DNV-->
        @endif
        </div>

        <div class="row">
          <div class="col-md-6 col-sm-12 mb-2">
            <span class="span_title">
              @trans('index.accion'):
            </span>
            @if($alarma->correccion)
              <span class="span_dato">
                @trans('index.corregir') @trans('redeterminaciones.corregir.' . $alarma->desencadenante->modelo)
              </span>
            @else
              <span class="span_dato">
                @trans('sol_redeterminaciones.acciones.' . $alarma->desencadenante->modelo)
              </span>
            @endif
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 col-sm-12 mb-2">
            <span class="span_title">
              @trans('forms.mensaje'):
            </span>
            <span class="span_dato">
              @trans('forms.titulo'): {{$alarma->titulo}}
            </span>
            <span class="span_dato">
              {!! $alarma-> mensaje !!}
            </span>
          </div>
        </div>
      </div>
        <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
          <div class="text-right">
            <a class="btn btn-small btn-success" href="{{ route('alarmas.solicitud') }}">@trans('forms.volver')</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
