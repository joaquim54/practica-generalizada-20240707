<?php
// Verificar si se recibieron los datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $name = $_POST['name'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $edad = $_POST['edad'] ?? '';

    // Validar que los datos no estén vacíos (puedes agregar más validaciones según sea necesario)
    if (empty($name) || empty($lastname) || empty($edad)) {
        $response = array(
            'success' => false,
            'message' => 'Por favor, complete todos los campos.'
        );
    } else {
        // Incluir archivo de conexión a la base de datos
        require_once('conexion.php');

        // Preparar la consulta SQL para insertar los datos
        $sql = "INSERT INTO personas (nombre, apellido, edad) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Verificar si la preparación de la consulta fue exitosa
        if ($stmt === false) {
            $response = array(
                'success' => false,
                'message' => 'Error al preparar la consulta: ' . $conn->error
            );
        } else {
            // Vincular los parámetros y ejecutar la consulta
            $stmt->bind_param("sss", $name, $lastname, $edad);

            if ($stmt->execute()) {
                $response = array(
                    'success' => true,
                    'message' => 'Datos insertados correctamente en la base de datos.'
                );
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'Error al insertar datos en la base de datos: ' . $stmt->error
                );
            }

            // Cerrar declaración
            $stmt->close();
        }

        // Cerrar conexión
        $conn->close();
    }

    // Enviar respuesta como JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>