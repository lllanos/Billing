@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
    <div class="row">
        <div class="col-md-12">
            <ol class="breadcrumb">
                <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
                <li><a href="{{ route('causantes.index') }}">@trans('index.causantes')</a></li>
                <li class="active">{!! trans('forms.crear').' '.trans('forms.causante') !!}</li>
            </ol>

            <div class="page-header">
                <h3>
                    {!! trans('index.crear').' '.trans('forms.causante') !!}
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
                    {{ Form::open(array('route' => array('causantes.store'), 'method' => 'POST')) }}

                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            {{ Form::label('nombre', trans('forms.nombre')) }}
                            {{ Form::text('nombre', Input::old('nombre'), array('class' => 'form-control', 'required', 'placeholder' => trans('forms.nombre'), 'autofocus')) }}
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            {{ Form::label('color', trans('forms.color')) }}
                            {{ Form::text('color', Input::old('color'), array('class' => 'form-control color-picker', 'required', 'placeholder' => trans('forms.color'))) }}
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="btn-group chk-group-btn p-0" data-toggle="buttons">
                            <label class="btn btn-primary btn-sm {!! Input::old('doble_firma') ? 'active' : '' !!}">
                                {{ Form::checkbox('doble_firma', 1, Input::old('doble_firma'), ['class' => 'doble_firma', 'id' => 'doble_firma']) }}
                                <span class="glyphicon glyphicon-ok"></span>
                            </label>

                            {{trans('forms.doble_firma')}}
                        </div>
                    </div>

                    <div class="doble-firma-group">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group form-group-chosen">
                                <label for="jefe_contrato_ar">@trans('forms.jefe_contrato_ar')</label>
                                {{ Form::select('jefe_contrato_ar', $usuarios, Input::old('jefe_contrato_ar'), ['class' => 'form-control chosen-select']) }}
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12">
                            <div class="form-group form-group-chosen">
                                <label for="jefe_contrato_py">@trans('forms.jefe_contrato_py')</label>
                                {{ Form::select('jefe_contrato_py', $usuarios, Input::old('jefe_contrato_py'), ['class' => 'form-control chosen-select']) }}
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12">
                            <div class="form-group form-group-chosen">
                                <label for="jefe_obras_ar">@trans('forms.jefe_obras_ar')</label>
                                {{ Form::select('jefe_obras_ar', $usuarios, Input::old('jefe_obras_ar'), ['class' => 'form-control chosen-select']) }}
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12">
                            <div class="form-group form-group-chosen">
                                <label for="jefe_obras_py">@trans('forms.jefe_obras_py')</label>
                                {{ Form::select('jefe_obras_py', $usuarios, Input::old('jefe_obras_py'), ['class' => 'form-control chosen-select']) }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
                        <div class="text-right">
                            <a class="btn btn-small btn-success" href="{{ route('causantes.index') }}">@trans('forms.volver')</a>
                            {{ Form::submit(trans('forms.guardar'), array('class' => 'btn btn-primary pull-right')) }}
                        </div>
                    </div>

                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#doble_firma').change(function() {
                if(this.checked)
                    $(".doble-firma-group").show();
                else
                    $(".doble-firma-group").hide();
            });

            $('#doble_firma').change();
        });
    </script>
@endsection
