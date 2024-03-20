@extends ('layout.app')

@section('title', config('app.name'))

@section('custom_styles')
  <style>
    .canvas {
      margin-left: 0 !important;
      width: 100% !important;
    }

		.offcanvas-icon {
			display: none;
		}
  </style>
@endsection

@section('content')

  <div class="row">
  	<div class="col-md-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
      </ol>
      <div class="page-header">
        <h3> RUTAS DE TEST </h3>
      </div>

      <div class="col-md-10 col-sm-12 col-md-offset-1">
        <div class="method-item">
          <h3 id="method_swap">
            <code>
              phpinfo <a href="{{route('test.phpinfo')}}"><i class="fa fa-external-link" title="IR"></i></a>
            </code>
          </h3>
          <div class="details">
            <div class="method-description">
              <p>Muestra PHP INFO</p>
            </div>
          </div>
        </div>

        <div class="method-item">
          <h3 id="method_swap">
            <code>
              runJobs <a href="{{route('test.runJobs')}}"><i class="fa fa-external-link" title="IR"></i></a>
            </code>
          </h3>
          <div class="details">
            <div class="method-description">
              <p>Ejecuta todos los Jobs encolados</p>
            </div>
          </div>
        </div>

        <div class="method-item">
          <h3 id="method_swap">
            <code>
              asociarAPublic <a href="{{route('test.asociarAPublic')}}"><i class="fa fa-external-link" title="IR"></i></a></a>
            </code>
          </h3>
          <div class="details">
            <div class="method-description">
              <p>Asocia TODOS los contratos a User de email: public@public.com</p>
            </div>
          </div>
        </div>

          <div class="method-item">
            <h3 id="method_swap">
              <code>
                asociarContratoIdAPublic <a href="{{route('test.asociarContratoIdAPublic', ['contrato_id' => 'X'])}}"><i class="fa fa-external-link" title="IR"></i></a></a>
              </code>
            </h3>
            <div class="details">
              <div class="method-description">
                <p>Asocia el contrato de id 'X' a User de email: public@public.com</p>
              </div>
              <div class="tags">
                <h4>Parámetros</h4>
                <table class="table table-condensed">
                  <tbody>
                    <tr>
                      <td>id de contrato (X)</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="method-item">
            <h3 id="method_swap">
              <code>
                reCalculoMontoYSaldo <a href="{{route('test.reCalculoMontoYSaldo', ['contrato_id' => 'X'])}}"><i class="fa fa-external-link" title="IR"></i></a></a>
              </code>
            </h3>
            <div class="details">
              <div class="method-description">
                <p>Recalcula Monto y Saldo vigente de los Contratos Moneda de X</p>
              </div>
              <div class="tags">
                <h4>Parámetros</h4>
                <table class="table table-condensed">
                  <tbody>
                    <tr>
                      <td>id de contrato (X)</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

      </div>
    </div>
  </div>

@endsection
