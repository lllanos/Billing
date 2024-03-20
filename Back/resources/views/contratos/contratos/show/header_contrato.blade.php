<div class="titulo__contenido">
  @trans('index.contrato') {{$contrato->numero_contrato}}
</div>
<div class="buttons-on-title">
  @if($contrato->borrador)
    @permissions('contrato-edit', 'contrato-edit-borrador')
    <div class="button_desktop">
      <a class="btn btn-success pull-right" href="{{route('contratos.edit', ['id' => $contrato->id]) }}">
        @trans('forms.editar') @trans('index.contrato')
      </a>
    </div>

    <div class="button_responsive">
      <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
        <button class="btn btn-primary dropdown-toggle" id="dd_menu" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
          <i class="fa fa-ellipsis-v"></i>
        </button>

        <ul class="dropdown-menu pull-right">
          <li>
            <a href="{{route('contratos.edit', ['id' => $contrato->id]) }}">
              @trans('forms.editar') @trans('index.contrato')
            </a>
          </li>
        </ul>
      </div>
    </div>
    @endpermission

  @elseif ($dobleFirma && (!$contrato->firma_ar || !$contrato->firma_py))
    @permissions(('contrato-edit'))
      <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
        <button class="btn btn-primary dropdown-toggle" id="dd_menu" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
          <i class="fa fa-ellipsis-v"></i>
        </button>

        <ul class="dropdown-menu pull-right">
          <li>
            <a href="{{route('contratos.edit', ['id' => $contrato->id]) }}">
              @trans('forms.editar') @trans('index.contrato')
            </a>
          </li>

          @if(
            (Auth::user()->id == $firmaAr && !$contrato->firma_ar)
            || (Auth::user()->id == $firmaPy && !$contrato->firma_py)
          )
          <li>
            <a class="action" href="{{route('contratos.firmar', ['id' => $contrato->id]) }}">
              @trans('forms.firmar') @trans('index.contrato')
            </a>
          </li>

          <li>
            <a class="action" href="{{route('contratos.borrador', ['id' => $contrato->id]) }}">
              @trans('forms.contrato_volver_borrador')
            </a>
          </li>
          @endif

        </ul>
      </div>
    @endpermission
  @else
    <div>
      @if(
        (Auth::user()->can('contrato-edit'))
        || ($contrato->permite_adendas) || ($contrato->permite_ampliaciones_de_obra)
        || ($contrato->falta_completar_empalme && Auth::user()->can('empalme-manage'))
        || ($contrato->has_requiere_garantia && Auth::user()->can('garantias-manage'))
        || (Auth::user()->can('anticipos-create'))
        || $contrato->permite_certificados
      )
        <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
          <button class="btn btn-primary dropdown-toggle" id="dd_menu" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
            <i class="fa fa-ellipsis-v"></i>
          </button>

          <ul class="dropdown-menu pull-right">
            @if(Auth::user()->can('contrato-edit'))
              <li>
                <a href="{{route('contratos.edit', ['id' => $contrato->id]) }}">
                  @trans('forms.editar') @trans('index.contrato')
                </a>
              </li>
            @endif

            @if($contrato->permite_adendas)
              <li>
                <a href="{{route('adenda.create', ['contrato_id' => $contrato->id])}}">
                  @trans('index.solicitar') @trans('contratos.adenda')
                </a>
              </li>
            @endif

            @if($contrato->permite_ampliaciones_de_obra)
              <li>
                <a href="{{route('ampliacion.create', ['contrato_id' => $contrato->id])}}">
                  @trans('index.solicitar') @trans('contratos.ampliacion_reprogramacion')
                </a>
              </li>
            @endif

            @permissions(('anticipos-create'))
            <li class="btn_add_anticipo" data-toggle="modal" data-target="#anticipoAddModal">
              <a> @trans('index.agregar') @trans('forms.anticipo')</a>
            </li>
            @endpermission

            @if($contrato->has_requiere_garantia)
              @permissions(('garantias-manage'))
              <li class="btn_add_garantia" data-toggle="modal" data-target="#garantiaAddModal">
                <a>@trans('index.validar') @trans('contratos.garantia')</a>
              </li>
              @endpermission
            @endif

            @if($contrato->permite_certificados)
              <li>
                <a href="{{route('certificado.create', ['contrato_id' => $contrato->id, 'empalme' => false])}}">
                  @trans('index.solicitar') @trans('contratos.certificado')
                  @trans('index.mes') {{count($contrato->certificados()->whereRedeterminado(0)->get()) + 1}}
                </a>
              </li>
            @endif
          </ul>
        </div>
      @endif
    </div>
  @endif
</div>
