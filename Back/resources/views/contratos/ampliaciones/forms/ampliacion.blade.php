<div class="col-md-6 col-sm-12">
  <div class="form-group">
    <label>{{trans('contratos.plazo_obra')}} ({{trans_choice('contratos.plazo.' . $ampliacion->contrato->tipo_plazo->nombre, 2)}})</label>
    <input type='number' value="{{$ampliacion->plazo}}"id='plazo' name='plazo' class='form-control' placeholder='{{trans('contratos.plazo_obra')}}'>
    <small class="msg_sugerencia_input text-success">{{trans('adendas.completar_plazo')}}</small>
  </div>
</div>
