<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style>
		@page{
			margin-top: 220px;
		}
		body {
			margin: 0 auto;
			font-family: 'Times New Roman';
		}
		*{
			background: none !important;
			color: black !important;
		}
		header {
			position: fixed;
			left: 0px;
			top: -190px;
			right: 0px;
			height: 220px;
			text-align: center;
			margin-bottom: 40px;
		}
		header .contenedor_logopdf {
			width: 250px;
			float: left;
			text-align: center;
		}
		header .contenedor_logopdf > span{
			font-family: italic;
		}
		header .contenedor_fecha_logopdf{
			width: 400px;
			float: right;
			font-size: 13px;
		}
		.contenedor_img_logo{
			margin: 0 auto;
			width: 50px;
		}
		.contenedor_ttl_principal {
			text-align: center;
			clear: both;
			margin-top: 200px;
		}
		.container {
		}
		table {
			border-collapse: collapse;
			margin: 0 auto;
			width: 100% !important;
		}
	</style>
</head>
<body>
	<header>
		<div class="contenedor_logopdf" style="text-align: center;">
			<div class="contenedor_img_logo" width="90px" style="display: block; margin: 0 auto;">
				<img src="img/logo_pdf.jpg" width="100%">
			</div>
			<span style="font-family: italic; text-align: center;">{!!nl2br($datos_acta->titulo)!!}</span>

		</div>
		<div class="contenedor_fecha_logopdf" style="text-transform: uppercase;">
			{{ $datos_acta->encabezado }}
		</div>
		<div style="width: 100%; clear: both;">
			<h3 style="text-align: center; margin-bottom: 0">ACTA DE REDETERMINACIÓN DE PRECIOS</h3>
			<h5 style="text-align: center; margin-top: 0">{{$subtitulo}}</h5>
		</div>
	</header>
	<div class="container">
		{!! $acta !!}
	</div>
</body>
</html>
