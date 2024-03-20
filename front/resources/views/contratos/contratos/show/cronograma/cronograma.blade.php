@if($cronograma != null && !$cronograma->borrador)
  @include('contratos.contratos.show.cronograma.show')
@else
  @if($cronograma == null)
    <div class="sin_datos">
      <h1 class="text-center">@trans('index.no_datos')</h1>
    </div>
  @else
    @include('contratos.contratos.show.cronograma.show')
  @endif
@endif
