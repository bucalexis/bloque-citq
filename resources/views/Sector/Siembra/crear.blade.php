@include('Partials.ScriptsGenerales.scriptsPartials')
<body>

<section id="container" >
    <!-- **********************************************************************************************************************************************************
    TOP BAR CONTENT & NOTIFICATIONS
    *********************************************************************************************************************************************************** -->
    <!--header start-->
    @include('Partials.ScriptsGenerales.headerPartials')
    <!--header end-->

    <!-- **********************************************************************************************************************************************************
    MAIN SIDEBAR MENU
    *********************************************************************************************************************************************************** -->
    <!--sidebar start-->
    @include('Sector.Siembra.aside')
    <!--sidebar end-->

    <section id="container">

        <section id="main-content">
            <section class="wrapper site-min-height">
                <h3>
                    <a href="{{ route('sector/siembra') }}">
                        <button type="button" class="btn btn-primary">
                            <i class="glyphicon glyphicon-arrow-left"></i>
                            Búsqueda
                        </button>
                    </a>
                </h3>
                <div class="row mt">

                    <!-- INICIO CONSULTAR FUNCIONES -->
                    <div class="col-lg-12">
                        <div class="form-panel">

                            @include('Partials.Mensajes.mensajes')

                            {!! Form::open(['action'=>['siembraSectorController@crear'],'class'=>'form-horizontal','role'=>'form','id'=>'formulario'])!!}

                            <h4 style="color:#078006"><i class="fa fa-angle-right"></i>Crear siembra</h4><br>

                            @include('Sector.Siembra.Partials.form')

                            {!! Form::close() !!}
                        </div>

                    </div>
                    <!-- FIN CONSULTAR FUNCIONES -->
                </div>
            </section>
        </section>
    </section>
</section>

    @include('Sector.Siembra.Partials.validator')
    <!--@include('Sector.Siembra.Partials.Script') <!--Javascript para ocultar fechaTerminacion si el status es activo-->
    @include('Partials.ScriptsGenerales.scriptsPartialsAbajo')
