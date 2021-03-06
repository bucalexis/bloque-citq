<script type="text/javascript">

    $(document).ready(function() {

        $('#formulario').bootstrapValidator({
            message: 'Los valores no son válidos',
            feedbackIcons: {
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                invernadero:{
                    validators: {
                        notEmpty: {
                            message: 'Seleccione una opción'
                        }
                    }
                },
                contenedor:{
                    validators: {
                        notEmpty: {
                            message: 'Seleccione una opción'
                        }
                    }
                },
                cultivo: {
                    validators: {
                        notEmpty: {
                            message: 'Seleccione una opción'
                        }
                    }
                },
                variedad:{
                    validators: {
                        stringLength: {
                            max: 255,
                            message: 'Debe ser menor de 255 carácteres'
                        }
                    }
                },
                numPlantas: {
                    validators: {
                        greaterThan:{
                            value: 0,
                            message: 'El número tiene que ser positivo'
                        },
                        integer:{
                            message: 'Ingrese numero entero'
                        }
                    }
                },
                destino:{
                    validators: {
                        notEmpty: {
                            message: "Seleccione una opción"
                        }
                    }
                },
                sustrato:{
                    validators: {
                        stringLength: {
                            max: 255,
                            message: 'Debe ser menor de 255 carácteres'
                        }
                    }
                },
                variedad:{
                    validators: {
                        stringLength: {
                            max: 255,
                            message: 'Debe ser menor de 255 carácteres'
                        }
                    }
                },
                fecha:{
                    validators: {
                        notEmpty: {
                            message: 'Seleccione una opción'
                        },

                        date: {
                            format: 'DD/MM/YYYY',
                            message: 'Ingrese fecha en formato dd/mm/aaaa'
                        }
                    }
                },
                status: {
                    validators: {
                        notEmpty: {
                            message: 'Seleccione una opción'
                        }
                    }
                },
                fechaTerminacion: {
                    validators: {
                        date: {
                            format: 'DD/MM/YYYY',
                            message: 'Ingrese fecha en formato dd/mm/aaaa'
                        }
                    }
                }
            }
        });

        $('#fecha')
                .on('dp.change dp.show', function(e) {
                    $('#formulario').data('bootstrapValidator').revalidateField('fecha');
                });

        $('#fechaTerminacion')
                .on('dp.change dp.show', function(e) {
                    $('#formulario').data('bootstrapValidator').revalidateField('fechaTerminacion');
                });


    });
</script>

<script type="text/javascript">
    $(function () {
        $('#fecha').datetimepicker({
            format:'DD/MM/YYYY'
        });
        $('#fechaTerminacion').datetimepicker({
            format:'DD/MM/YYYY'
        });

        $('#fecha').keypress(function(event) {event.preventDefault();});
        $('#fechaTerminacion').keypress(function(event) {event.preventDefault();});
    });
</script>
