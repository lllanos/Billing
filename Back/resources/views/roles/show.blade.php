@extends('layout.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>{{ $role->name }}</h2>
                </div>
                <div class="pull-right">
                    <a class="btn btn-primary" href="{{ route('seguridad.roles.index') }}">Volver</a>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{trans('index.permisos')}}:</strong>
                    @if(!empty($rolePermissions))
                        @foreach($rolePermissions as $rp)
                            <label class="label label-success">{{trans('permisos.'.$rp->name)}}</label>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
