@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
<div class="row">
  <div class="col-md-12">
    <h3>
      {!!trans('index.list_of')!!} {!!trans('forms.usuarios')!!}
      <div class="pull-right">
        @permission(('user-create'))
          <a class="btn btn-success" href="{{ URL::to('seguridad/users/create') }}">
            {!!trans('forms.nuevo')!!} {!!trans('index.usuario')!!}
          </a>
        @endpermission
      </div>
    </h3>

    <ol class="breadcrumb">
      <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
      <li><a href="{{url('/seguridad/users')}}">{{trans('index.usuarios')}}</a></li>
      <li class="active">{!!trans('index.list_of')!!} {!!trans('forms.usuarios')!!}</li>
    </ol>
    <div class="col-md-12 col-sm-12">
      <div class="row list-table">
        <table class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th>{{trans('forms.name')}}</th>
              <th>{{trans('forms.mail')}}</th>
              <th class="text-center">{!! trans('forms.acciones') !!}</th>
              </tr>
          </thead>
          <tbody>
              @foreach($users as $key => $value)
                <tr>
                  <td>{{ $value->apellido_nombre }}</td>
                  <td>{{ $value->email }}</td>
                  <td class="text-center acciones-td">
                      @permission(('user-edit'))
                          <a href="{{ URL::to('seguridad/users/' . $value->id . '/edit') }}" alt="@trans('index.editar')"><i class="glyphicon glyphicon-pencil"></i></a>
                      @endpermission
                      @permission(('user-delete'))
                          {!! Form::open(['method' => 'DELETE', 'url' => 'seguridad/users/' . $value->id, 'style'=>'display:inline']) !!}
                            <a href="javascript:void(0);" onclick="$(this).closest('form').submit();"><i class="glyphicon glyphicon-remove"></i></a>
                          {!! Form::close() !!}
                      @endpermission
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
