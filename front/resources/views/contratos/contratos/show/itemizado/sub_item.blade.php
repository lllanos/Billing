  <div class="panel-group colapsable_sub m-0 pt-1" id="accordion_sub{{$sufijo}}{{$subItem->id}}" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
      @if ($subItem->is_hoja)
        <div class="panel-heading panel_heading_collapse p-0" role="tab" id="headingOne_sub{{$sufijo}}{{$subItem->id}}">
          <h4 class="panel-title titulo_collapse titulo_collapse_small panel_heading_{{$tab}} m-0 panel_title_btn">
            @if(isset($isExcel))
              DATOS EXCEL
            @else
              @php ($item = $subItem)
              @include('contratos.contratos.show.itemizado_cronograma.fila', ['header' => false, 'subheader' => false])
              @include('contratos.contratos.show.itemizado_cronograma.options.hoja')
            @endif
          </h4>
        </div>
      @else
        <div class="panel-heading panel_heading_collapse p-0" role="tab" id="headingOne_sub{{$sufijo}}{{$subItem->id}}">
          <h4 class="panel-title titulo_collapse titulo_collapse_small panel_heading_{{$tab}} m-0 panel_title_btn">
            @if(isset($isExcel))
              DATOS EXCEL
            @else
              @php ($item = $subItem)
              @include('contratos.contratos.show.itemizado_cronograma.fila', ['header' => false, 'subheader' => false])
              @include('contratos.contratos.show.itemizado_cronograma.options.nodo')
            @endif
          </h4>
        </div>

        <div id="collapse_sub{{$sufijo}}{{$subItem->id}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_sub{{$sufijo}}{{$subItem->id}}">
          <div class="panel-body panel_sub_tablas p-0 asdfsdfd">
            @if(count($subItem->child))
              @php ($level = $subItem)
              @foreach($level->child as $subItem)
                @php ($tab++)
                @include('contratos.contratos.show.itemizado.sub_item')
              @endforeach
              @php ($subItem = $level)
            @endif
          </div>
        </div>
    @endif
  </div>
</div>
