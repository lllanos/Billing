@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
    <div class="row">
        <div class="col-md-12">
            <ol class="breadcrumb">
                <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
                <li><a href="{{ route('causantes.index') }}">@trans('index.causantes')</a></li>
                <li class="active">{!! trans('forms.editar').' '.trans('forms.causante') !!}</li>
            </ol>
            <div class="page-header">
                <h3>
                    {!! trans('index.editar').' '.trans('forms.causante') !!}
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
                    {{ Form::model($causante, ['route' => ['causantes.update', $causante->id], 'method' => 'POST']) }}

                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            {{ Form::label('nombre', trans('forms.nombre')) }}
                            {{ Form::text('nombre', Input::old('nombre'), ['class' => 'form-control', 'required', 'placeholder' => trans('forms.nombre'), 'autofocus']) }}
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            {{ Form::label('color', trans('forms.color')) }}
                            {{ Form::text('color', Input::old('color'), ['class' => 'form-control color-picker', 'required', 'placeholder' => trans('forms.color')]) }}
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="btn-group chk-group-btn p-0" data-toggle="buttons">
                            <label class="btn btn-primary btn-sm {{ Input::old('doble_firma', $causante->doble_firma) ? 'active' : '' }}">
                                {{ Form::checkbox('doble_firma', 1, Input::old('doble_firma', $causante->doble_firma), ['class' => 'doble_firma', 'id' => 'doble_firma']) }}
                                <span class="glyphicon glyphicon-ok"></span>
                            </label>
                            {{ trans('forms.doble_firma') }}
                        </div>
                    </div>

                    <div class="doble-firma-group">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group form-group-chosen">
                                <label for="jefe_contrato_ar">@trans('forms.jefe_contrato_ar')</label>

                                {!! Form::select('jefe_contrato_ar', $usuarios, Input::old('jefe_contrato_ar', $causante->jefe_contrato_ar), ['class' => 'form-control chosen-select', 'data-original' => $causante->jefe_contrato_ar]) !!}
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12">
                            <div class="form-group form-group-chosen">
                                <label for="jefe_contrato_py">@trans('forms.jefe_contrato_py')</label>

                                {!! Form::select('jefe_contrato_py', $usuarios, Input::old('jefe_contrato_py', $causante->jefe_contrato_py), ['class' => 'form-control chosen-select', 'data-original' => $causante->jefe_contrato_py]) !!}
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12">
                            <div class="form-group form-group-chosen">
                                <label for="jefe_obras_ar">@trans('forms.jefe_obras_ar')</label>

                                {!! Form::select('jefe_obras_ar', $usuarios, Input::old('jefe_obras_ar', $causante->jefe_obras_ar), ['class' => 'form-control chosen-select', 'data-original' => $causante->jefe_obras_ar]) !!}
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12">
                            <div class="form-group form-group-chosen">
                                <label for="jefe_obras_py">@trans('forms.jefe_obras_py')</label>

                                {!! Form::select('jefe_obras_py', $usuarios, Input::old('jefe_obras_py', $causante->jefe_obras_py), ['class' => 'form-control chosen-select', 'data-original' => $causante->jefe_obras_py]) !!}
                            </div>
                        </div>

                        <div class="col-sm-12 comment-wrapper">
                            <div class="form-group">
                                <label for="comment">@trans('forms.comment')</label>

                                {!! Form::textarea('comment', '', ['class' => 'form-control', 'id' => 'comment']) !!}
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
            let dobleFirma = $("#doble_firma");
            let dobleFirmaGroup = $(".doble-firma-group");
            let commentWrapper = $(".comment-wrapper");
            let comment = $("#comment");
            let dobleFirmaSelects = dobleFirmaGroup.find('select');

            dobleFirma.change(function() {
                if (this.checked) {
                    dobleFirmaGroup.show();
                    updateSelect();
                }
                else {
                    dobleFirmaGroup.hide();
                    commentWrapper.hide();
                    comment.prop('required', false);
                }
            });

            dobleFirma.change();

            dobleFirmaSelects.each(function () {
                $(this).change(updateSelect);
            });

            function updateSelect() {
                commentWrapper.hide();
                comment.prop('required', false);

                dobleFirmaSelects.each(function () {
                    let $this = $(this);

                    console.log(parseInt($this.val()), $this.data('original'))

                    if (parseInt($this.val()) !== $this.data('original')) {
                        commentWrapper.show();
                        comment.prop('required', true);
                    }
                });
            }
        });
    </script>
@endsection
