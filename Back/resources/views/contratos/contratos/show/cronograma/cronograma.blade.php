@if($cronograma != null && !$cronograma->borrador)
  @include('contratos.contratos.show.cronograma.show')
@else
  @cant(('cronograma-edit'))
    @if($cronograma == null || count($cronograma->composiciones) == 0)
      <div class="col-md-12 col-sm-12">
        <div class="row">
          <h1 class="text-center">{{trans('contratos.sin.cronograma')}}</h1>
        </div>
      </div>
    @else
      @include('contratos.contratos.show.cronograma.show')
    @endif
  @endcant

  @permissions(('cronograma-edit'))
    @if(isset($valueContratoMoneda->itemizado->cronograma))
      @php ($itemizado = $valueContratoMoneda->itemizado->cronograma)
      @php ($keyItemizado = $itemizado->id)
      @php ($fromCronograma = true)
      @php ($sufijo = '_cro_')
    @endif
    @include('contratos.contratos.show.cronograma.edit')
  @endpermission
@endif
