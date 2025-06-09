<?php
session_start();
header('Content-Type: application/json');

// Cambia a la ruta correcta según tu proyecto:
require_once(__DIR__ . "/../db.php"); // Ajusta si tu archivo de conexión está en otro path

$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$usuario_id = 1; // TEST: pon fijo mientras debuggeas. Luego pon el $_SESSION que corresponda.

if (!isset($_FILES['files'])) {
    echo json_encode([
        "success" => false,
        "error" => "No files received",
        "debug" => $_FILES
    ]);
    exit;
}

$responses = [];
foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
    $originalName = $_FILES['files']['name'][$key];
    $fileType = $_FILES['files']['type'][$key];
    $fileSize = $_FILES['files']['size'][$key];
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $uniqueName = uniqid() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $originalName);
    $targetFile = $uploadDir . $uniqueName;
    $relativePath = 'uploads/' . $uniqueName;

    // Debug: ¿existe el archivo temporal?
    if (!is_uploaded_file($tmp_name)) {
        $responses[] = [
            "success" => false,
            "error" => "El archivo temporal no existe o no es válido",
            "tmp_name" => $tmp_name
        ];
        continue;
    }

    if (move_uploaded_file($tmp_name, $targetFile)) {
        // Debug: ¿existe el archivo destino?
        if (!file_exists($targetFile)) {
            $responses[] = [
                "success" => false,
                "error" => "El archivo no se guardó en destino",
                "targetFile" => $targetFile
            ];
            continue;
        }

        // Inserta en BD
        require_once(__DIR__ . "/../db.php"); // Asegura conexión
        $sql = "INSERT INTO archivos (nombre_archivo, ruta_archivo, extension, tipo_archivo, tamano, ruta, usuario_id, fecha_subida)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $responses[] = [
                "success" => false,
                "error" => "Prepare failed: " . $conn->error
            ];
            continue;
        }
        $stmt->bind_param("ssssisi", $originalName, $relativePath, $extension, $fileType, $fileSize, $relativePath, $usuario_id);
        if ($stmt->execute()) {
            $responses[] = [
                "success" => true,
                "message" => "Archivo subido y registrado: $originalName"
            ];
        } else {
            $responses[] = [
                "success" => false,
                "error" => "Insert failed: " . $stmt->error
            ];
        }
        $stmt->close();
    } else {
        $responses[] = [
            "success" => false,
            "error" => "move_uploaded_file failed",
            "tmp_name" => $tmp_name,
            "targetFile" => $targetFile
        ];
    }
}
echo json_encode([
    "success" => !in_array(false, array_column($responses, 'success')),
    "results" => $responses
]);
?>
