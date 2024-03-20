@extends ('layout.app')

@section('title', config('app.name'))

@section('content')
<div class="row">
  <div class="col-md-12">
    <ol class="breadcrumb">
      <li><a href="{{url('/')}}">Tests</a></li>
      <li class="active">Simulador Variación Polinómica</li>
    </ol>
    <div class="page-header">
      <h3>
        Calcular Saltos
      </h3>
    </div>

			<div class="panel panel-default">
			  <div class="panel-body">
          <form method="POST" data-action="{{ route('redeterminaciones.calcularSaltos') }}" id="form-ajax2">
            {{ csrf_field() }}

            <div class="col-md-12 col-sm-12">
              <div class="form-group">
                <label for="contrato_id">@trans('index.contrato')</label>
                <select class="form-control select-html-change" name="contrato_id" id="contrato_id" required>
                  <option disabled selected value> {{trans('forms.select.contrato')}}</option>
                  @foreach($contratos_select as $opcion)
                    <option value="{{$opcion['id']}}" >{{$opcion['value']}} </option>
                  @endforeach
                </select>
              </div>
            </div>

            <span class="link-response"></span>
            <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
              <div class="text-right">
                <a class="btn btn-small btn-success" href="{{ url('seguridad/usuarios') }}">@trans('forms.volver')</a>
                {{ Form::submit('Calcular', array('class' => 'btn btn-primary pull-right')) }}
              </div>
            </div>

			    </form>
			  </div>
			</div>
	  </div>
	</div>
@endsection

@section('scripts')
window.applyFormAjax2 = () => {
  $('#form-ajax2').off('submit');

  $('#form-ajax2').on('submit', function(e) {
    $('.help-block').remove();
    $('.form-group').removeClass('has-error');
    e.preventDefault();
    var action = $('#form-ajax2').data('action');
    $.ajax({
      url: action,
      type: 'POST',
      dataType: 'json',
      data: new FormData($('#form-ajax2')[0]),
      processData: false,
      contentType: false,
      success: function(resp) {
        loadingToggle();
        if(resp.status == true) {
          if(resp.message != undefined)
            modalCloseToastSuccess(resp.message);
            $('.link-response').html('').append('<a href="' + resp.link + '">Ver Contrato</a>')
        } else {
          if(Object.keys(resp.errores).length > 0) {
            mostrarErroresEnInput(resp.errores);
            window.scrollTo($('#form-ajax2').position());
          }

          if(resp.message.length > 0)
            modalCloseToastError(resp.message);
        }
      }
    });
  });
}
applyFormAjax2();
@endsection
