@if($empalme)
 <?php $certificados = $contrato->certificados_empalme; ?>
@else
 <?php $certificados = $contrato->certificados_basicos ?>

 @if($redeterminados)
   <?php $certificados = $contrato->certificados_redeterminados_tabla; ?>
 @endif
@endif

@if(!$redeterminados)
  @include('contratos.contratos.show.certificados.show.tabla_basicos')
@else
  @include('contratos.contratos.show.certificados.show.tabla_redeterminados')
@endif
