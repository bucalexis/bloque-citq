@include('Partials.ScriptsGenerales.scriptsPartials')
<body>
<script type="text/javascript">

    $(function () {
        //previene lo del input
        $('#fechaFinDP').keypress(function(event) {event.preventDefault();});
        //previene lo del input
        $('#fechaInicioDP').keypress(function(event) {event.preventDefault();});


        //VALIDAR FECHAS EN BUSQUEDA

        $('#fechaFinDP').datetimepicker({
            format: 'DD/MM/YYYY'
        });

        $('#fechaInicioDP').datetimepicker({
            format: 'DD/MM/YYYY'
        });

        $('#fechaInicioDP').datetimepicker();
        $('#fechaFinDP').datetimepicker({
            useCurrent: false //Important! See issue  #1075
        });
        $("#fechaInicioDP").on("dp.change", function (e) {
            $('#fechaFinDP').data("DateTimePicker").minDate(e.date);
        });
        $("#fechaFinDP").on("dp.change", function (e) {
            $('#fechaInicioDP').data("DateTimePicker").maxDate(e.date);
        });
    });
</script>

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
    @include('Plantula.SalidaPlanta.aside')
            <!--sidebar end-->

    <section id="container">
        <section id="main-content">
            <section class="wrapper site-min-height">
                <h3 style="color:#078006"><i class="fa fa-angle-right"></i>Salida de planta</h3>
                <div class="row mt">
                    <!-- INICIO CONTENIDO -->
                    <div class="col-lg-12">
                        <div class="form-panel">
                            <h4><i class="fa fa-angle-right"></i>Búsqueda</h4>
                            @include('Partials.Mensajes.mensajes')

                            <div class="form-group" align="right">
                                <a href="{{route('plantula/salidaplanta/crear')}}"> <button class="btn agregar tooltips" data-placement="left" data-original-title="Agregar"><i class="fa fa-plus"></i></i></button></a>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">

                                    {!! Form::open(['route' => 'plantula/salidaplanta/lista' ,'method'=>'GET']) !!}
                                    <div class="form-group">

                                        <div class="col-lg-2">
                                            <select  class="form-control" id="invernaderoPlantula" name="invernaderoPlantula" disabled>
                                                <option value="">Plántula 1</option>
                                                @if( isset($invernaderos))
                                                    @foreach($invernaderos as $invernadero)
                                                        @if($invernadero->id === 1)
                                                            <option value="" selected> {{ $invernadero->nombre}}  </option>
                                                        @else
                                                            <option value="{{  $invernadero->id  }}" > {{ $invernadero->nombre}}  </option>
                                                        @endif

                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>


                                    <div id="formulario">
                                        <div class="form-group">
                                            <div class="col-lg-2">
                                                <div class="input-group date" id="fechaDP">
                                                 <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                  </span>
                                                    {!!Form::text('fechaInicio' ,null,['class'=>'form-control','id'=>'fechaInicioDP','placeholder'=>'Fecha inicial'])!!}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-lg-2">
                                                <div class="input-group date" id="fechaDP">
                                                 <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                  </span>
                                                    {!!Form::text('fechaFin' ,null,['class'=>'form-control','id'=>'fechaFinDP','placeholder'=>'Fecha final'])!!}
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <button type="submit" class="btn btn-default" >
                                                <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                                                Buscar
                                            </button>

                                        </div>
                                        {!! Form::close() !!}
                                    </div>



                                    <hr>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-advance table-hover">
                                            <thead>
                                            <tr>
                                                <th><i class="fa fa-thumb-tack"></i> Invernadero Plantúla </th>
                                                <th> <i class="fa fa-pencil-square-o"></i> Comentario</th>
                                                <th><i class="fa fa-calendar" ></i> Fecha </th>

                                            </tr>
                                            </thead>
                                            <tbody>

                                            @if ( isset( $salidas) )

                                                @foreach( $salidas as $salida )

                                                    <tr>
                                                        <td>{{ $salida->invernadero->nombre }}</td>
                                                        <td>{{ $salida->comentario }}</td>
                                                        <td>{{ $salida->fecha }}</td>


                                                        <td style="width: 5px">
                                                            <a href="{{ route('plantula/salidaplanta/consultar/item',$salida->id) }}"><button class="btn btn-success btn-xs tooltips" data-placement="top" data-original-title="Consultar"><i class="fa fa-eye"></i></button></a>
                                                        </td>

                                                        <td style="width: 5px">
                                                            <a href="{{ route('plantula/salidaplanta/modificar/item',$salida->id) }}"><button class="btn btn-primary btn-xs tooltips" data-placement="top" data-original-title="Modificar"><i class="fa fa-pencil"></i></button></a>
                                                        </td>

                                                        <td style="width: 5px">
                                                            {!! Form::open(['action'=>['salidaDePlantaController@eliminar'],'role'=>'form'] )  !!}
                                                            <button class="btn btn-danger btn-xs tooltips" data-placement="top" data-original-title="Eliminar" onclick='return confirm("¿Seguro que desea eliminar la salida de planta?")'><i class="fa fa-trash-o "></i></button>
                                                            <input type="hidden" name="id" value={{$salida->id}}>
                                                            {!! Form::close() !!}
                                                        </td>

                                                    </tr>

                                                @endforeach

                                            @endif


                                            </tbody>
                                        </table>
                                    </div>
                                    @if (isset($salidas))
                                        {!! $salidas->setPath('')->appends(Input::query())->render()!!}
                                    @endif
                                </div>
                            </div>
                            <!-- FIN CONTENIDO -->

                        </div>
            </section>
        </section>
    </section>


    <script type="text/javascript">

        $(document).ready(function() {

            $('#formulario').bootstrapValidator({
                message: 'Los valores no son válidos',
                feedbackIcons: {
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {

                    fechaInicio: {
                        validators: {
                            notEmpty: {
                                message: 'Ingrese fecha'
                            },
                            date: {
                                format: 'DD/MM/YYYY',
                                message: 'Ingrese en formato dd/mm/aaaa'
                            }
                        }
                    },
                    fechaFin: {
                        validators: {
                            notEmpty: {
                                message: 'Ingrese fecha'
                            },
                            date: {
                                format: 'DD/MM/YYYY',
                                message: 'Ingrese en formato dd/mm/aaaa'
                            }
                        }
                    }
                }
            });

            $('#fechaInicioDP').on('dp.change dp.show', function(e) {
                if ( $('#formulario').data('bootstrapValidator').revalidateField('fechaInicio') && ! $('#formulario').data('bootstrapValidator').revalidateField('fechaFin')) {
                    $('#formulario').data('bootstrapValidator').revalidateField('fechaFin');
                }
                if($('#fechaInicioDP').val()=="")
                {
                    $('#formulario').data('bootstrapValidator').revalidateField('fechaInicio');
                }

                if($('#fechaInicioDP').val()==""&&$('#fechaFinDP').val()==""){
                    $('#formulario').data('bootstrapValidator').enableFieldValidators('fechaInicio',false);
                    $('#formulario').data('bootstrapValidator').enableFieldValidators('fechaFin',false);
                }
                else{
                    $('#formulario').data('bootstrapValidator').enableFieldValidators('fechaInicio',true);
                    $('#formulario').data('bootstrapValidator').enableFieldValidators('fechaFin',true);
                }


            });

            $('#fechaFinDP').on('dp.change dp.show', function(e) {
                if ( $('#formulario').data('bootstrapValidator').revalidateField('fechaFin') && ! $('#formulario').data('bootstrapValidator').revalidateField('fechaInicio')) {
                    $('#formulario').data('bootstrapValidator').revalidateField('fechaInicio');
                }
                if($('#fechaFinDP').val()=="")
                {
                    $('#formulario').data('bootstrapValidator').revalidateField('fechaFin');
                }
                if($('#fechaInicioDP').val()==""&&$('#fechaFinDP').val()==""){
                    $('#formulario').data('bootstrapValidator').enableFieldValidators('fechaInicio',false);
                    $('#formulario').data('bootstrapValidator').enableFieldValidators('fechaFin',false);
                }
                else{
                    $('#formulario').data('bootstrapValidator').enableFieldValidators('fechaInicio',true);
                    $('#formulario').data('bootstrapValidator').enableFieldValidators('fechaFin',true);
                }
            });
        });
    </script>



@include('Partials.ScriptsGenerales.scriptsPartialsAbajo')