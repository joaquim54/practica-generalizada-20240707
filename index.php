<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iteración de json</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div id="container" class="container-fluid">
        <div class="container text-center">
            <h1 class="h">Iteración de json</h1>
            <div id="mensaje"></div>
            <form id="formulario" method="POST" action="insertar_personas.php">
                <?php
                $archivo_json = 'datos.json';
                if (file_exists($archivo_json) && is_readable($archivo_json)) {
                    $archivo_json = file_get_contents($archivo_json);
                    $json_iterado = json_decode($archivo_json, true);
                    //verificar conexion exitosa
                    if ($json_iterado !== null && isset($json_iterado['personas'])) {
                        $personas = $json_iterado['personas'];
                        //select de los nombres
                        echo '<div class="mb-3">';
                        echo '<label for="name">Seleccione un nombre: </label>';
                        echo '<select id="name" name="name" class="form-select">';
                        echo '<option value="" disabled selected>Seleccione...</option>';
                        foreach ($personas as $persona) {
                            echo '<option value="' . htmlspecialchars($persona['nombre']) . '">' . htmlspecialchars($persona['nombre']) . '</option>';
                        }
                        echo '</select>';
                        echo '</div>';
                        // Select para los apellidos (inicialmente vacío)
                        echo '<div class="mb-3">';
                        echo '<label for="lastname" class="form-label">Seleccione un apellido</label>';
                        echo '<input id="lastname" name="lastname" type="text" class="form-control" readonly>';
                        echo '</div>';

                        // Select para la edad (inicialmente vacío)
                        echo '<div class="mb-3">';
                        echo '<label for="edad" class="form-label">Seleccione edad</label>';
                        echo '<input id="edad" name="edad" type="text" class="form-control" readonly>';
                        echo '</div>';
                    } else {
                        echo '<p>no se encuentran datos válidos en el JSON...</p>';
                    }
                } else {
                    echo '<p>No se pudo leer el archivo JSON.</p>';
                }
                ?>
                <div class="container text-center">
                    <button type="submit" class="btn btn-primary">Insertar en la base de datos</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Manejar el envío del formulario
            $('#formulario').submit(function(event) {
                event.preventDefault(); // Evitar el envío tradicional del formulario

                // Mostrar SweetAlert de confirmación antes de enviar
                Swal.fire({
                    title: '¿Seguro que quieres ingresar el dato?',
                    text: "Una vez ingresado, no podrás deshacer esta acción.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, ingresar dato',
                    cancelButtonText: 'No',
                    backdrop: 'rgba(0,0,0,0.8)', // Fondo oscuro con transparencia
                    allowOutsideClick: false, // Evitar cerrar haciendo clic fuera del modal
                    allowEscapeKey: false // Evitar cerrar presionando la tecla Esc
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Obtener datos del formulario
                        var formData = {
                            'name': $('#name').val(),
                            'lastname': $('#lastname').val(),
                            'edad': $('#edad').val()
                        };

                        // Enviar datos al servidor usando Ajax
                        $.ajax({
                            type: 'POST',
                            url: 'insertar_personas.php', // PHP que procesa la inserción
                            data: formData,
                            dataType: 'json', // Tipo de datos esperado de la respuesta
                            encode: true
                        }).done(function(data) {
                            // Manejar la respuesta del servidor
                            if (data.success) {
                                // Mostrar SweetAlert de éxito
                                Swal.fire({
                                    title: '¡Dato ingresado exitosamente!',
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 1500 // Tiempo en milisegundos (1.5 segundos)
                                });
                                $('#mensaje').html('<div class="alert alert-success mt-3" role="alert">Registro insertado</div>');
                            } else {
                                $('#mensaje').html('<div class="alert alert-danger mt-3" role="alert">No se pudo insertar el registro</div>');
                            }
                        }).fail(function(jqXHR, textStatus, errorThrown) {
                            console.error('Error al enviar la solicitud: ' + textStatus, errorThrown);
                            $('#mensaje').html('<div class="alert alert-danger mt-3" role="alert">Error al conectar con el servidor</div>');
                        });
                    }
                });
            });

            // Manejar el cambio en el select de nombres
            $('#name').change(function() {
                var selectedName = $(this).val();

                // Buscar la persona seleccionada en el JSON
                var selectedPerson = <?php echo json_encode($personas); ?>;

                // Encontrar la persona que coincide con el nombre seleccionado
                var selectedPersonData = null;
                for (var i = 0; i < selectedPerson.length; i++) {
                    if (selectedPerson[i].nombre === selectedName) {
                        selectedPersonData = selectedPerson[i];
                        break;
                    }
                }

                // Verificar si se encontró la persona y actualizar los campos
                if (selectedPersonData) {
                    $('#lastname').val(selectedPersonData.apellido);
                    $('#edad').val(selectedPersonData.edad);
                } else {
                    console.error('No se encontró la persona seleccionada.');
                }
            });
        });
    </script>
</body>

</html>