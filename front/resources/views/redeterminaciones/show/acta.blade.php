<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style>
		body {
			margin: 0 auto;
			font-family: 'Times New Roman';
		}
		header{
			margin-bottom: 40px;
		}
		header .contenedor_logopdf {
			width: 250px;
			float: left;			
			text-align: center;
		}
		header .contenedor_fecha_logopdf {
			width: 400px;
			float: right;
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
			width: 100%;
			margin: 0 auto;
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
			<span>{!!$datos_acta->titulo!!}</span>
		</div>
		<div class="contenedor_fecha_logopdf">
			{{ $datos_acta->encabezado }}
		</div>
	</header>
	<div class="container" style="clear: both;">
		<h3 style="text-align: center;">ACTA DE REDETERMINACIÓN DE PRECIOS</h3>
		{!! $acta !!}
	</div>















	{{--
	<header>
		<div class="contenedor_logopdf" style="background: red;">
			<div class="contenedor_img_logo" style="background-color: blue">
				<img src="img/logo_pdf">				
			</div>
			<span>{{ $datos_acta->titulo }} Ministerios de Transporte Dirección Nacional de Vialidad</span>
		</div>
		<div class="contenedor_fecha_logopdf">
			{{ $datos_acta->encabezado }}
		</div>
	</header>
	<div class="contenedor_ttl_principal">
		<h3 class="titulo_principal">ACTA DE REDETERMINACIÓN DE PRECIOS</h3>
	</div>
	<div class="container">
		{!! $acta !!}
	</div>--}}
</body>
</html>
