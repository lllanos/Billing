<table class="table table-striped table-hover table-bordered zui-table">
  <thead>
    <tr>
      <th class="text-center"></th>
      <th>@trans('certificado.nr_certificado_th')</th>
      <th>@trans('certificado.avance_certificado')</th>
      <th>@trans('certificado.avance_acumulado')</th>
      <th>@trans('certificado.importe_certificado')</th>
      <th>@trans('certificado.importe_acumulado')</th>
      @if(!$empalme)
        <th>@trans('forms.estado')</th>
      @endif
      <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
    </tr>
  </thead>
  <tbody class="tbody_js">
    @foreach($certificados as $certificado)
      <tr id="certificado_{{$certificado->id}}">
        <td class="text-center">
          @if($certificado->es_borrador)
            <i class="fa fa-eraser" data-toggle="tooltip" data-placement="bottom" title="@trans('index.borrador')"></i>
          @elseif($certificado->solicitado_a_validar OR $certificado->solicitado_a_corregir)
            <i class="fa fa-star-half-empty" data-toggle="tooltip" data-placement="bottom" title="{{$certificado->estado['nombre_trans']}}"></i>
          @endif
        </td>
        <td>
          {{$certificado->mes_show}} - {{$certificado->mesAnio('fecha', 'Y-m-d')}}</td>
        <td>
          @foreach($certificado->certificados_por_moneda as $keyContratoMoneda => $valueContratoMoneda)
            <span class="badge">
              {{$valueContratoMoneda['simbolo']}} @toDosDec($certificado->avanceCertificadoPorMoneda($keyContratoMoneda)) %
            </span>
          @endforeach
        </td>
        <td>
          @foreach($certificado->certificados_por_moneda as $keyContratoMoneda => $valueContratoMoneda)
            <span class="badge">
              {{$valueContratoMoneda['simbolo']}} @toDosDec($certificado->avanceAcumuladoPorMoneda($keyContratoMoneda)) %
            </span>
          @endforeach
        </td>
        <td>
          @foreach($certificado->certificados_por_moneda as $keyContratoMoneda => $valueContratoMoneda)
            <span class="badge">
             {{$valueContratoMoneda['simbolo']}} @toDosDec($certificado->montoPorMoneda($keyContratoMoneda))
            </span>
          @endforeach
        </td>
        <td>
          @foreach($certificado->certificados_por_moneda as $keyContratoMoneda => $valueContratoMoneda)
            <span class="badge">
              {{$valueContratoMoneda['simbolo']}} @toDosDec($certificado->importeAcumuladoPorMoneda($keyContratoMoneda))
            </span>
          @endforeach
        </td>
        @if(!$empalme)
          <td>
            <span class="badge badge-referencias" style="background-color:#{{$certificado->estado['color']}};">
              {{$certificado->estado['nombre_trans']}}
            </span>
            @if($certificado->empalme)
              <span class="badge" style="background-color:var(--green-redeterminacion-color);">
                @trans('contratos.empalme')
              </span>
            @endif
          </td>
        @endif
        <td class="actions-col noFilter">
          <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
              <i class="fa fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu pull-right">
              <li><a href="{{route('certificado.ver', ['id' => $certificado->id]) }}"><i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')</a></li>
              @if(!(!$certificado->borrador OR !$certificado->puede_editar))
                <li> <a href="{{route('certificado.edit', ['id' => $certificado->id]) }}"> <i class="fa fa-pencil"></i> @trans('index.editar')</a> </li>
              @endif
              @if(!$certificado->borrador)
                <li> <a href="{{route('export.certificado', ['id' => $certificado->id]) }}"> <i class="glyphicon glyphicon-save-file"></i> @trans('index.descargar') </a> </li>
              @endif
            </ul>
          </div>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>
