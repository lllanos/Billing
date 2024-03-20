<!-- Paginado por ajax -->
@if ($paginator->hasPages())
<div class="pagination-content">
  <div class="pagination">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
      <span class="disabled mouse-pointer"><span>&laquo;</span></span>
    @else
      <span class="mouse-pointer">
        <a class="paginator-ajax" data-url="{{ route('certificacion.listadoInscriptos', ['id' => ':id_replace']) }}"  data-url="{{ route('certificacion.listadoInscriptos', ['id' => ':id_replace']) }}" data-paginator="{{ $paginator->previousPageUrl() }}" rel="prev" title="{{trans('pagination.previous')}}" id="pag_ant">&laquo;</a>
      </span>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
      {{-- "Three Dots" Separator --}}
      @if (is_string($element))
        <span class="disabled mouse-pointer"><span>{{ $element }}</span></span>
      @endif

      {{-- Array Of Links --}}
      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <span class="active mouse-pointer"><span>{{ $page }}</span></span>
          @else
            <span class="mouse-pointer"><a class="paginator-ajax"  data-url="{{ route('certificacion.listadoInscriptos', ['id' => ':id_replace']) }}" data-paginator="{{ $url }}" id="pag_{{$page}}">{{ $page }}</a></span>
          @endif
        @endforeach
      @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
      <span class="mouse-pointer"><a class="paginator-ajax"  data-url="{{ route('certificacion.listadoInscriptos', ['id' => ':id_replace']) }}" data-paginator="{{ $paginator->nextPageUrl() }}" rel="next" title="{{trans('pagination.next')}}" id="pag_next">&raquo;</a></span>
    @else
      <span class="disabled mouse-pointer"><span>&raquo;</span></span>
    @endif
  </div>
</div>
@endif
