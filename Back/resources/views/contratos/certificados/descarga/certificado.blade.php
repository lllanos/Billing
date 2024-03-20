@php($colspanLong = 9)
@php($colspanShort = 4)
@if(!$certificado->is_basico)
     @php($colspanLong++)
     @php($colspanShort++)
@endif
<!DOCTYPE html>
<html>
<head>
  <title></title>
  <style>
	@page{
		margin-top: 180px;
		margin: 200px 30px auto;
	}
    body {
      margin: 0 auto;
      font-size: 80%;
      font-family: Arial, sans-serif;
    }
	header {
	   position: fixed;
	   left: 0px;
	   top: -150px;
	   right: 0px;
	   height: 70px;
	   border-bottom: 1px solid #000;
	}
  	.page-break {
	   page-break-after: always;
	}
	.container{
	   margin-top: -50px;
	   margin-bottom: 70px;
	}
    .text-align-right{
      text-align: right;
    }
    .logo{
	   margin-top: -10px;
    }
    #oscuro-blanco{
    	background-color: #999999;
    	color: #fff;
    }
    #oscuro{
    	background-color: #bdbdbd;
    }
    #oscuro-dos{
    	background-color: #e0e0e0;
    }
    #oscuro-tres{
    	background-color: #fafafa;
    }
	.bold{
		font-weight: 400;
	}
    table {
      border-collapse: collapse;
      margin: 0 auto;
      width: 100% !important;
    }
    tr, td, th {
      border: 1px solid #000;
      padding: 2px;
    }
  </style>
</head>
<body>
  <header>
    <div style="float: left; width: 25%;">
      <div style="padding: 5px;">
        <img class="logo" src="./img/main-logo-eby-arg.png" width="280">
      </div>
    </div>
    <div style="float: left; width: 74%">
      <div style="padding: 5px; float: right;">
          NRO APROBACIÓN: {{$certificado->contrato->numero_contrato}} - {{$certificado->id}}
          <br>
          SUPERVISOR DE OBRA: @foreach($certificado->contrato->representantes_tecnicos_eby as $representante)
				               {{$representante->nombre}} {{$representante->apellido}}
				              @endforeach
          <br>
          FECHA DE APROBACIÓN TÉCNICA: {{$certificado->fecha_aprobacion}}
        </div>
    </div>
    <div style="clear:both"><span></span></div>
  </header>
  	@php($i = 0)
  	@foreach($certificados_por_moneda as $keyCertMoneda => $valueCertMoneda)
  	 @foreach($valueCertMoneda['certificados'] as $keyPorContratista => $valuePorContratista)
  	 @php($i++)
	 @if($i > 1)
	 <div class="page-break"></div>
	 @endif
  	  <div class="container">
	    <div style="width: 100%; margin-bottom: 30px;">
	      {{$certificado->contrato->numero_contratacion}} - {{$certificado->contrato->denominacion}}
	      <br>
	      CERTIFICADO NRO: {{ $certificado->mes_show }} - {{$certificado->mesAnio('fecha', 'Y-m-d')}} – @if(!$certificado->is_basico) REDETERMINACIÓN {{str_pad($certificado->redeterminacion->nro_salto, 3, "0", STR_PAD_LEFT)}} @else BÁSICO @endif
	      <br>
	      PERIODO: {{$certificado->mesAnio('fecha', 'Y-m-d')}}
	      <br>
	      FECHA DE PRESENTACIÓN: {{$certificado->contrato->fecha_oferta}}
	      <br>
	      MONEDA: {{$valueCertMoneda['nombre']}}
	      <br>
	      CONTRATISTA: {{$valuePorContratista->contratista->nombre_documento}}
	    </div>

	    <table>
	      <thead>
	        <tr id="oscuro-blanco">
	          <th>ITEM</th>
	          <th>DESCRIPCION</th>
	          <th>CANTIDAD</th>
	          <th>UM</th>
	          <th>IMPORTE UNITARIO CONTRACTUAL</th>
	          <th>IMPORTE TOTAL CONTRACTUAL</th>
	          <th>% AVANCE ANT.</th>
	          <th>% AVANCE ACT.</th>
	          <th>% AVANCE ACUM.</th>
	          @if(!$certificado->is_basico)
	          <th>IMPORTE REDET.</th>
	          @endif
	          <th>MONTO CERT. ANT.</th>
	          <th>MONTO CERT. ACT.</th>
	          <th>MONTO CERT. ACUM.</th>
	        </tr>
	      </thead>
	      <tbody>
	        @foreach($valuePorContratista->items as $item)
	        	<tr @if(strlen($item->item->codigo) == 4) id="oscuro" @elseif(strlen($item->item->codigo) == 7) id="oscuro-dos" @elseif(strlen($item->item->codigo) == 10) id="oscuro-tres" @endif>
		          <td>{{$item->item->codigo}}</td>
		          <td>{{$item->item->descripcion}}</td>
		          @if($item->item->is_hoja)
					<td class="text-align-right">@toDosDec($item->cantidad)</td>
					<td class="text-align-right">{{$item->item->porc_unidad_medida}}</td>
					<td class="text-align-right">@toDosDec($item->item->monto_unitario)</td>
					<td class="text-align-right">@toDosDec($item->item->monto_total)</td>
					<td class="text-align-right">@toDosDec($item->acumulado_anterior) {{$item->item->porc_unidad_medida}}</td>
					<td class="text-align-right">@toDosDec($item->cantidad) {{$item->item->porc_unidad_medida}}</td>
					<td class="text-align-right">@toDosDec($item->cantidad + $item->acumulado_anterior)  {{$item->item->porc_unidad_medida}}</td>
					@if(!$certificado->is_basico)
					<td class="text-align-right">@toDosDec($item->item->precio_redeterminado_item($certificado->redeterminacion->id)->precio)</td>
					@endif
					<td class="text-align-right">@toDosDec($item->montoItemCertAnterior($item))</td>
					<td class="text-align-right">@toDosDec($item->monto)</td>
					<td class="text-align-right">@toDosDec($item->montoItemCertAnterior($item) + $item->monto)</td>
		          @else
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					@if(!$certificado->is_basico)
					<td></td>
					@endif
					<td></td>
					<td></td>
					<td></td>
	              @endif
		        </tr>
	        @endforeach	   
	        <tr id="oscuro" style="height: 50px" class="bold">
	          <td colspan="{{$colspanLong}}">Totales</td>
	          <td class="text-align-right">@toDosDec($valuePorContratista->monto_bruto_total_cert_anterior)</td>
	          <td class="text-align-right">@toDosDec($valuePorContratista->monto_bruto)</td>
	          <td class="text-align-right">@toDosDec($valuePorContratista->monto_bruto_total_cert_anterior + $valuePorContratista->monto_bruto)</td>
	        </tr>
	        <tr id="oscuro-dos" class="bold">
	         <td colspan="{{$colspanShort}}">TOTAL MONTO CONTRACTUAL</td>
	         <td class="text-align-right" colspan="8">@toDosDec($valuePorContratista->itemizado->total)</td>
	        </tr>
	        @if(!$certificado->is_basico)
	         <tr id="oscuro-dos" class="bold">
	         <td colspan="{{$colspanShort}}">TOTAL MONTO REDETERMINADO</td>
	         <td class="text-align-right" colspan="8">@toDosDec($certificado->redeterminacion->monto_redeterminado)</td>
	        </tr>
	        @endif
	        <tr id="oscuro-dos" class="bold">
	         <td colspan="{{$colspanShort}}">% AVANCE FÍSICO ACUMULADO</td>
	         <td class="text-align-right" colspan="8">@if($valuePorContratista->monto_bruto && $valuePorContratista->itemizado->total)@toDosDec(($valuePorContratista->monto_bruto / $valuePorContratista->itemizado->total)*100)@endif %</td>
	        </tr>

            @if(!$certificado->is_basico)
	        <tr id="oscuro-dos" class="bold">
	         <td colspan="{{$colspanShort}}">MONTO BRUTO DEL CERTIFICADO REDETERMINADO</td>
	         <td class="text-align-right" colspan="8">@toDosDec($valuePorContratista->monto_bruto)</td>
	        </tr>
	        <tr class="bold">
	         <td colspan="{{$colspanShort}}">MONTO BRUTO DEL CERTIFICADO ANTERIOR</td>
	         <td class="text-align-right" colspan="8">@toDosDec($valuePorContratista->monto_redeterminacion_anterior)</td>
	        </tr>
	        
			@if($valuePorContratista->penalidad)
			<tr class="bold">
	         <td colspan="{{$colspanShort}}">DEDUCCION PENALIDAD POR DESVIO</td>
	         <td class="text-align-right" colspan="8">@toDosDec($valuePorContratista->penalidad)</td>
	        </tr>
	        @endif

	        <tr id="oscuro-dos" class="bold">
	         <td colspan="{{$colspanShort}}">MONTO BRUTO CERTIFICADO ACTUAL</td>
	         <td class="text-align-right" colspan="8">@toDosDec($valuePorContratista->monto_bruto - $valuePorContratista->monto_redeterminacion_anterior)</td>
	        </tr>
	        <tr class="bold">
	         <td colspan="{{$colspanShort}}">DEDUCCIÓN ANTICIPO FINANCIERO @if($valuePorContratista->item_anticipo) (@toDosDec($valuePorContratista->item_anticipo->porcentaje)%) @else 0% @endif</td>
	         <td class="text-align-right" colspan="8"> @if($valuePorContratista->item_anticipo) @toDosDec(($valuePorContratista->monto_bruto - $valuePorContratista->monto_redeterminacion_anterior) * $valuePorContratista->item_anticipo->porcentaje_100) @else 0 @endif</td>
	        </tr>
	        <tr id="oscuro-blanco" class="bold">
	         <td colspan="{{$colspanShort}}">TOTAL MONTO A FACTURAR</td>
		         @if($valuePorContratista->item_anticipo)
		         <td class="text-align-right" colspan="8">@toDosDec(($valuePorContratista->monto_bruto - $valuePorContratista->monto_redeterminacion_anterior) - (($valuePorContratista->monto_bruto - $valuePorContratista->monto_redeterminacion_anterior) * $valuePorContratista->item_anticipo->porcentaje_100))</td>
		         @else
		         <td class="text-align-right" colspan="8">@toDosDec($valuePorContratista->monto_bruto - $valuePorContratista->monto_redeterminacion_anterior)</td>	         
		         @endif
	        </tr>
	        @else
			<tr class="bold">
	         <td colspan="{{$colspanShort}}">MONTO BRUTO DEL PRESENTE CERTIFICADO</td>
	         <td class="text-align-right" colspan="8">@toDosDec($valuePorContratista->monto_bruto)</td>
	        </tr>
	        <tr class="bold">
	         <td colspan="{{$colspanShort}}">DEDUCCIÓN ANTICIPO FINANCIERO @if($valuePorContratista->item_anticipo) (@toDosDec($valuePorContratista->item_anticipo->porcentaje)%) @else 0% @endif</td>
	         <td class="text-align-right" colspan="8">@toDosDec($valuePorContratista->monto_bruto - $valuePorContratista->monto)</td>
	        </tr>
	        <tr id="oscuro-blanco" class="bold">
	         <td colspan="{{$colspanShort}}">TOTAL MONTO A FACTURAR</td>
	         <td class="text-align-right" colspan="8">@toDosDec($valuePorContratista->monto)</td>
	        </tr>		        
	        @endif
	      </tbody>
	    </table>
	  </div>
	  <div style="position:absolute; bottom: 10px">
	    <div style="float: left; width: 25%; border-top: 1px solid #000; padding-top: 10px;">
	      Firma Representante {{$valuePorContratista->contratista->fantasia_razon_social}}
	    </div>
	    <div style="float: left; width: 74%">

	    </div>
	    <div style="clear:both"><span></span></div>
	  </div>
	 @endforeach
  	@endforeach
</body>
</html>
