@permissions(('cronograma-view'))
  @php($itemizado = $cronograma)
  @php ($keyItemizado = $itemizado->id)
  @php ($fromCronograma = true)
  @php ($sufijo = '_cro_')
  @include('contratos.contratos.show.itemizado.itemizado')
@endpermission
