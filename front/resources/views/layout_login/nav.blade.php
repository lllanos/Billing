<nav class="navbar navbar-default navbar-fixed-top">
	<div class="container-fluid">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="{{ url('/') }}" alt="">
                            <img src="{{asset('img/main-logo-eby-arg.png')}}" />
			</a>
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="main">
                    <ul class="nav navbar-nav navbar-right">

                        <li>
                            <a href="{{ url('/') }}">Dashboard</a>
                        </li>

                        <li>
                            <a href="{{ url('seguridad/users') }}">Usuarios</a>
                        </li>
                    </ul>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>