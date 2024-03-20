@if($valueInstancia->fecha_inicio != null && $valueInstancia->inicio_fin != null)
  <div class="hist_instancia_icon">
    <i class="fa fa-calendar" aria-hidden="true"></i> 
    <label>{{ $valueInstancia->inicio_fin}}</label>
  </div>
@endif
