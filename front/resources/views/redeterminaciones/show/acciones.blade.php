@if($redeterminacion->puede_cargar_poliza)
  <div class="button_desktop">
    <a class="btn btn-success open-modal-redeterminacion" href="javascript:void(0);" id="btn_gestion"
    data-instancia="CargaPolizaCaucion" data-id="{{$redeterminacion->id}}">
      {!!trans('redeterminaciones.acciones.CargaPolizaCaucion')!!}
    </a>
  </div>
  <div class="button_responsive">
    <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
      <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="{{trans('index.opciones')}}">
        <i class="fa fa-ellipsis-v"></i>
      </button>
      <ul class="dropdown-menu pull-right">
        <li>
          <a class="open-modal-redeterminacion" href="javascript:void(0);" id="btn_gestion_rsp"
            data-instancia="CargaPolizaCaucion" data-id="{{$redeterminacion->id}}">
            {!!trans('redeterminaciones.acciones.CargaPolizaCaucion')!!}
          </a>
        </li>
      </ul>
    </div>
  </div>
@endif
