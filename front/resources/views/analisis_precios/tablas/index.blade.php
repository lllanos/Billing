{{-- container items --}}
<div id="items" class="tab-pane fade in active">
  @include('analisis_precios.tablas.items')
</div>
{{-- fin container items --}}



<div id="materiales_directos" class="tab-pane fade">
  @include('analisis_precios.tablas.materiales_directos')
</div>
<div id="materiales_comerciales" class="tab-pane fade">
  @include('analisis_precios.tablas.materiales_comerciales')
</div>


<div id="materiales_explotados" class="tab-pane fade">
  @include('analisis_precios.tablas.materiales_explotados')
</div>

<div id="materiales_obra" class="tab-pane fade">
  @include('analisis_precios.tablas.mano_obra')
</div>

{{-- container coeficiente resumen --}}
<div id="coeficiente_resumen" class="tab-pane fade">
  <div class="panel-group acordion colapsable_cero" id="accordion-coeficiente" role="tablist" aria-multiselectable="true">
    @include('analisis_precios.tablas.coeficiente')
  </div>
</div>
{{-- fin container coeficiente resumen --}}