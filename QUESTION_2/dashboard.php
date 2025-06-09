<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['usuario'])) {
  header('Location: login.php');
  exit;
}
$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Rheonics Web Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
  <!-- Usa la versión 0.149.0 de Three.js y sus loaders (FUNCIONA DIRECTO) -->
<!-- Three.js primero -->
<script src="https://cdn.jsdelivr.net/npm/three@0.149.0/build/three.min.js"></script>
<!-- three-step-parser después -->
<script src="https://cdn.jsdelivr.net/npm/three-step-parser@1.0.1/dist/step.min.js"></script>
<!-- Tu preview3d.js al final -->
<script src="js/preview3d.js"></script>

</head>

<body>
  <!-- Sidebar -->
  <nav id="sidebar" class="sidebar expanded">
    <div class="sidebar-header">
      <img id="sidebarLogo" class="logo" src="img/RHEONICS WHITE LOGO.png" alt="Logo" />
    </div>
    <ul class="nav flex-column pt-3" id="sidebarLinks">
      <li class="nav-item">
        <a class="nav-link active" data-tab="dashboard_home" href="#" title="Dashboard">
          <i class="bi bi-house-door"></i>
          <span class="ms-2 link-label" data-i18n="dashboard">Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-tab="database" href="#" title="Database">
          <i class="bi bi-database"></i>
          <span class="ms-2 link-label" data-i18n="database">Database</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-tab="reports" href="#" title="Reports">
          <i class="bi bi-bar-chart-line"></i>
          <span class="ms-2 link-label" data-i18n="reports">Reports</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-tab="settings" href="#" title="Settings">
          <i class="bi bi-gear"></i>
          <span class="ms-2 link-label" data-i18n="settings">Settings</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-tab="upload" href="#" title="Upload">
          <i class="bi bi-cloud-upload"></i>
          <span class="ms-2 link-label" data-i18n="upload">Upload</span>
        </a>
      </li>

    </ul>
    <a class="nav-link log-out-link mt-auto" href="logout.php" title="Logout">
      <i class="bi bi-box-arrow-right"></i>
      <span class="ms-2 link-label" data-i18n="logout">Logout</span>
    </a>
  </nav>

  <!-- Main content -->
  <div class="main-content">
    <!-- Top Bar -->
    <nav class="navbar navbar-dark bg-transparent shadow-none py-2 px-3 align-items-center justify-content-between">
      <div class="d-toggleSidebar">
        <button class="btn btn-link p-0 ms-auto me-1" id="toggleSidebar" title="Toggle sidebar">
          <i class="bi bi-list" style="font-size:1.6rem;"></i>
        </button>
      </div>
      <div class="d-flex align-items-center gap-2">
        <button id="themeBtn" class="btn btn-icon" title="Switch Theme">
          <i class="bi bi-moon"></i>
        </button>
        <div class="dropdown" id="langDropdownHeader">
          <button class="btn btn-icon dropdown-toggle" type="button" data-bs-toggle="dropdown" title="Change language"
            id="langDropdownBtn">
            <i class="bi bi-translate"></i> <span id="currentLangName" class="d-none d-md-inline">English</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end" id="langDropdownMenu">
            <li><label class="dropdown-item lang-radio"><input type="radio" name="lang" value="en" checked>
                English</label></li>
            <li><label class="dropdown-item lang-radio"><input type="radio" name="lang" value="es"> Español</label></li>
          </ul>
        </div>
        <span class="user-badge bg-primary rounded-circle text-white ms-2">
          <?= strtoupper(substr($usuario['nombre'] ?? 'M', 0, 1)) ?>
        </span>
      </div>
    </nav>

    <!-- Contenedor dinámico de vistas -->
    <div id="mainTabContent"></div>

    <span id="currentDateTime" class="p-2"></span>
  </div>

  <!-- Librerías externas -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    AOS.init();
  </script>

  <!-- Carga Three.js UNA sola vez -->
  <script src="https://cdn.jsdelivr.net/npm/three@0.149.0/build/three.min.js"></script>
  <!-- Carga three-step-parser para STEP -->
  <script src="https://cdn.jsdelivr.net/npm/three-step-parser@1.0.1/dist/step.min.js"></script>
  <!-- Tu script de preview 3D -->
  <script src="js/preview3d.js"></script>

  <script src="js/script.js"></script>
</body>

</html>