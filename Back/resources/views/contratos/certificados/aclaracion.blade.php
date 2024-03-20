@if(!$certificado->redeterminado && $certificado->cant_solicitudes_aprobadas > 0)
  @php($total = $certificado->primera_redeterminacion + $certificado->cant_solicitudes_aprobadas)
  @if(!$certificado->empalme)
	@trans('certificado.mensajes.confirmacion_validar_aclaracion', ['mes' => $certificado->mes])
	@for($i = $certificado->primera_redeterminacion; $i < $total ; $i++)
	<b>{{$certificado->mes}}-{{str_pad($i, 3, "0", STR_PAD_LEFT)}}</b>@if($i < $total - 2), @elseif($i < $total - 1) @trans('index.y') @endif
	@endfor
	@trans('certificado.mensajes.confirmacion_validar_aclaracion_2')
  @endif
@endif
