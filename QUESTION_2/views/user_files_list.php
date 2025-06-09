<?php
file_put_contents("debug_modal.txt", "Recibido usuario_id=".json_encode($_GET).PHP_EOL, FILE_APPEND);

require_once("../db.php"); // Ajusta la ruta si es necesario

$usuario_id = isset($_GET['usuario_id']) ? intval($_GET['usuario_id']) : 0;

if (!$usuario_id) {
    echo "<div class='alert alert-warning'>No user selected.</div>";
    exit;
}

$sql = "SELECT * FROM archivos WHERE usuario_id = ? ORDER BY fecha_subida DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-info'>No files uploaded by this user.</div>";
} else {
    echo "<table class='table table-hover'>";
    echo "<thead><tr>
            <th>Name</th>
            <th>Extension</th>
            <th>Type</th>
            <th>Size (KB)</th>
            <th>Date</th>
            <th>Download</th>
          </tr></thead><tbody>";
    while ($row = $result->fetch_assoc()) {
        $sizeKB = number_format($row['tamano'] / 1024, 2);
        echo "<tr>
                <td>".htmlspecialchars($row['nombre_archivo'])."</td>
                <td>".htmlspecialchars($row['extension'])."</td>
                <td>".htmlspecialchars($row['tipo_archivo'])."</td>
                <td>{$sizeKB}</td>
                <td>".htmlspecialchars($row['fecha_subida'])."</td>
                <td><a href='../".htmlspecialchars($row['ruta'])."' download class='btn btn-sm btn-outline-primary'>Download</a></td>
              </tr>";
    }
    echo "</tbody></table>";
}
$stmt->close();
$conn->close();
?>
