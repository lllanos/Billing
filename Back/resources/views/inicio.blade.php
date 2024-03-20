@extends ('layout.app')

@section('title', config('app.name'))

@section('content')

@if($userWidgets != null)
	<div class="row">
		<div class="col-md-12">
			<h3>
				@trans('index.inicio')
				<div class="pull-right buttons-on-title dashboard-btn-on-title">
					<div class="dropdown dd-layout pull-left">
						<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
		        	<i class="glyphicon glyphicon-th-large"></i>
		        </button>
						<ul class="dropdown-menu pull-right">
							@foreach ($layouts as $layoutKey => $valueLayout)
								<li>
									<a class="@if(Auth::user()->layout->id == $valueLayout->id) layout-selected @endif" href="{{ route('update.layout', ['id' => $valueLayout->id]) }}">
							      <div class="layout-{{$valueLayout->nombre}}" name="imgLayout" border="0"></div>
								  </a>
								</li>
							@endforeach
		        </ul>
					</div>
			    <a class="btn btn-primary pull-left" href="javasciprt:void(0);" data-toggle="modal" data-target="#modalDashboard" data-title="Widgets">
			      <i class="glyphicon glyphicon-edit"></i>
			    </a>
			  </div>
			</h3>
		</div>
	</div>
	<div class="row">
		<div class="dashboard col-md-12">
		  @include('dashboard.widgets_layouts.'.$layout)
		</div>
	</div>
@endif

@endsection

@section('modals')
	<div id="modalDashboard" class="modal fade" tabindex="-1" role="dialog">
	  <div class="modal-dialog modal-lg">
			@include('dashboard.editWidgets')
	  </div>
	</div>
@endsection

@section('scripts')
  $(document).ready(() => {
		$(".img-widget").click(function () {
      $(this).parent().find('.chk-widget').trigger('click');
      $(this).toggleClass('widget-seleccionado');
    });

		@if($userWidgets != null)
			@foreach ($userWidgets as $widgetKey => $widget)
			  url = {!! json_encode('widget/dashboard/' . $widget->nombre) !!};
			  var nombre = {!! json_encode($widget->nombre) !!};

				$.ajax({
				  url: url,
				  cache: false,
				  contentType: false,
				  processData: false,
				  method: 'GET',
				  dataType: 'html',
				  success: function(response) {
						var nombre = {!! json_encode($widget->nombre) !!};
						$('.content-' + nombre).html(response);
				  },
				});

			@endforeach
		@endif
  });
@endsection
