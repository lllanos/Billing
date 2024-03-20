@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
<div class="row">
  <div class="col-md-12">
  <ol class="breadcrumb">
    <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
    <li><a href="{{ route('motivos.index') }}">{{trans('index.motivos')}}</a></li>
    <li class="active">{!! trans('forms.editar').' '.trans('forms.motivo') !!}</li>
  </ol>
  <div class="page-header">
    <h3>
      {!! trans('index.editar').' '.trans('forms.motivo') !!}
    </h3>
  </div>

      @if (count($errors) > 0)
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
            	<li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

			<div class="panel panel-default">
			  <div class="panel-body">
           <form role="form" method="POST" action="{{route('motivos.update', $motivo->id )}}">
            {{ csrf_field() }}
            <input type='text' id='user_modifier_id' name='user_modifier_id' class='hidden' value="{{ Auth::user()->id }}">
            <div class="col-md-6 col-sm-12">
              <div class="form-group">
                <label>{{trans('forms.descripcion')}}*</label>
                <input type='text' id='descripcion' name='descripcion' class='form-control'  value="{{ $motivo->descripcion }}" >
              </div>
            </div>

            <div class="col-md-6 col-sm-12">
              <div class="form-group form-group-chosen">
                <label for="responsable">{{trans('forms.responsable')}}*</label>
                <select class="form-control" name="responsable" id="responsable">
                    <option value="Externa" @if($motivo->responsable == 'Externa') selected @endif>Externa</option>
                    <option value="Contratista" @if($motivo->responsable == 'Contratista') selected @endif>Contratista</option>
                    <option value="EBY" @if($motivo->responsable == 'EBY') selected @endif>EBY</option>
                </select>
              </div>
            </div>
            <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
              <div class="text-right">
                <a class="btn btn-small btn-success" href="{{ route('motivos.index') }}">@trans('forms.volver')</a>
                {{ Form::submit(trans('forms.guardar'), array('class' => 'btn btn-primary pull-right')) }}
              </div>
            </div>
           </form>
			  </div>
			</div>
	  </div>
	</div>
@endsection
