@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
<div class="row">
  <div class="col-md-12">
    <h3>
      {!!trans('index.list_of')!!} {!!trans('forms.solicitudes')!!}
      <div class="pull-right">
          @permission(('user-create'))
              <a class="btn btn-success" href="{{ URL::to('solicitudes/create') }}">
                {!!trans('forms.nuevo')!!} {!!trans('index.solicitud')!!}
              </a>
          @endpermission
      </div>
    </h3>

    <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li><a href="{{url('/solicitudes')}}">{{trans('index.solicitudes')}}</a></li>
        <li class="active">{!!trans('index.list_of')!!} {!!trans('forms.solicitudes')!!}</li>
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
              @foreach ($solicitudes as $key => $solicitud)
                <tr>
                  <td>{{ $solicitud->name }}</td>
                  <td>{{ $solicitud->description }}</td>
                  <td class="text-center acciones-td">

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
