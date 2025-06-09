<?php
require_once '../db.php';
if (!isset($usuario)) {
  // Si accedes directamente, puedes obtener usuario de sesión
  session_start();
  $usuario = $_SESSION['usuario'] ?? ['nombre' => 'Manager'];
}
?>

<div class="container-fluid p-4 pb-5">
  <h4><span data-i18n="greeting">Good afternoon, </span> <?= htmlspecialchars($usuario['nombre']) ?>!</h4>
  <div class="text-muted mb-4" data-i18n="welcome">Welcome to your manager dashboard</div>
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card info-card text-center" data-aos="fade-in" data-aos-duration="1500" data-aos-delay="0">
        <div class="card-body">
          <i class="bi bi-people-fill fa-2x mb-2 text-info" style="font-size:2rem;"></i>
          <div class="card-title fs-6" data-i18n="activeUsers">Active Users</div>
          <?php
          $userCountResult = $conn->query("SELECT COUNT(*) as total FROM usuarios");
          $userCount = $userCountResult ? ($userCountResult->fetch_assoc()['total'] ?? 0) : 0;
          ?>
          <div class="fs-4 fw-bold"><?= $userCount ?></div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card info-card text-center" data-aos="fade-in" data-aos-duration="1500" data-aos-delay="50">
        <div class="card-body">
          <i class="bi bi-hdd-network fa-2x mb-2 text-success" style="font-size:2rem;"></i>
          <div class="card-title fs-6" data-i18n="databaseSize">Database Size</div>
          <?php
          $sizeResult = $conn->query("SELECT SUM(tamano) as total FROM archivos");
          $size = $sizeResult ? ($sizeResult->fetch_assoc()['total'] ?? 0) : 0;
          $size_human = $size >= 1073741824 ? round($size / 1073741824, 2) . ' GB' : ($size >= 1048576 ? round($size / 1048576, 2) . ' MB' : round($size / 1024, 2) . ' KB');
          ?>
          <div class="fs-4 fw-bold"><?= $size_human ?></div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card info-card text-center" data-aos="fade-in" data-aos-duration="1500" data-aos-delay="100">
        <div class="card-body">
          <i class="bi bi-shield-lock fa-2x mb-2 text-purple" style="font-size:2rem;"></i>
          <div class="card-title fs-6" data-i18n="securityScore">Security Score</div>
          <div class="fs-4 fw-bold">92%</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card info-card text-center" data-aos="fade-in" data-aos-duration="1500" data-aos-delay="150">
        <div class="card-body">
          <i class="bi bi-lock-fill fa-2x mb-2 text-warning" style="font-size:2rem;"></i>
          <div class="card-title fs-6" data-i18n="authRequests">Auth Requests</div>
          <div class="fs-4 fw-bold">2.4k</div>
        </div>
      </div>
    </div>
  </div>
  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="card h-100" data-aos="fade-in" data-aos-duration="1500" data-aos-delay="200">
        <div style="position: relative;">
          <div class="card-header"><i class="bi bi-database"></i> <span data-i18n="databaseActivity">Database Activity</span>
          </div>
          <a href="#" class="btn btn-link p-0 see-more" data-target="database"><span data-i18n="seeMore">See more</span> <i class="bi bi-box-arrow-in-up-right"></i></a>
        </div>
        <div class="card-body text-muted small" style="min-height:240px;padding: 2px;">
          <div class="table-responsive">
            <table class="table table-sm table-dark">
              <thead>
                <tr>
                  <th data-i18n="filename">Filename</th>
                  <th data-i18n="type">Type</th>
                  <th data-i18n="size">Size</th>
                  <th data-i18n="uploadedBy">Uploaded By</th>
                  <th data-i18n="date">Date</th>
                </tr>
              </thead>
              <tbody>
                <?php
                require_once __DIR__ . '/../db.php';
                $sql = "SELECT a.*, u.nombre_completo FROM archivos a
                        LEFT JOIN usuarios u ON a.usuario_id = u.id
                        ORDER BY a.fecha_subida DESC LIMIT 6";
                $rs = $conn->query($sql);
                if ($rs && $rs->num_rows > 0) {
                  while ($f = $rs->fetch_assoc()) {
                    echo "<tr>
                      <td><i class='bi bi-file-earmark-text'></i> " . htmlspecialchars($f['nombre_archivo']) . "</td>
                      <td>" . htmlspecialchars($f['tipo_archivo']) . "</td>
                      <td>" . round($f['tamano'] / 1024, 1) . " KB</td>
                      <td>" . htmlspecialchars($f['nombre_completo'] ?? 'N/A') . "</td>
                      <td>" . date('d/m/Y H:i', strtotime($f['fecha_subida'])) . "</td>
                    </tr>";
                  }
                } else {
                  echo "<tr><td colspan='5' data-i18n='noFilesUploaded'>No files uploaded yet.</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card h-100" data-aos="fade-in" data-aos-duration="1500" data-aos-delay="300">
        <div style="position: relative;">
          <div class="card-header"><i class="bi bi-bar-chart-line"></i> <span data-i18n="userStatistics">User Statistics</span>
          </div>
          <a href="#" class="btn btn-link p-0 see-more" data-target="reports"><span data-i18n="seeMore">See more</span> <i class="bi bi-box-arrow-in-up-right"></i></a>
        </div>
        <div class="card-body text-muted small" style="min-height:240px;padding: 2px;">
          <div class="table-responsive">
            <table class="table table-sm table-dark">
              <thead>
                <tr>
                  <th data-i18n="user">User</th>
                  <th data-i18n="filesUploaded">Files uploaded</th>
                </tr>
              </thead>
                <tbody>
                  <?php
                  $rs = $conn->query("SELECT u.id, u.nombre_completo, COUNT(a.id) as files 
                      FROM usuarios u LEFT JOIN archivos a ON u.id = a.usuario_id 
                      GROUP BY u.id ORDER BY files DESC");
                  while ($row = $rs->fetch_assoc()) {
                    echo "<tr>
                      <td>{$row['nombre_completo']}</td>
                      <td>{$row['files']}</td>
                    </tr>";
                  }
                  ?>
                </tbody>

            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="card" data-aos="fade-in" data-aos-duration="1500" data-aos-delay="300">
    <div class="card-header" data-i18n="recentActivity">Recent Activity</div>
    <ul class="list-group list-group-flush">
      <?php
      $rs = $conn->query("SELECT a.*, u.nombre_completo FROM actividad a
                        LEFT JOIN usuarios u ON a.usuario_id = u.id
                        ORDER BY a.fecha DESC LIMIT 10");
      if ($rs && $rs->num_rows > 0) {
        while ($act = $rs->fetch_assoc()) {
          echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
                <span>
                  <i class='bi bi-person-circle me-2'></i>
                  {$act['descripcion']}
                  <span class='text-muted small ms-2'>" . tiempo_transcurrido($act['fecha']) . "</span>
                </span>
                <span class='badge bg-info'>" . ucfirst($act['tipo']) . "</span>
              </li>";
        }
      } else {
        echo "<li class='list-group-item'>No recent activity.</li>";
      }

      // Función para mostrar tiempo transcurrido
      function tiempo_transcurrido($datetime)
      {
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;
        if ($diff < 60) return $diff . ' sec ago';
        if ($diff < 3600) return floor($diff / 60) . ' min ago';
        if ($diff < 86400) return floor($diff / 3600) . ' h ago';
        return date('d/m/Y H:i', $timestamp);
      }
      ?>
    </ul>
  </div>
</div>
