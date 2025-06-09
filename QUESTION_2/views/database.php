<?php
require_once '../db.php';
?>
<div class="container-fluid p-4 pb-6">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 data-i18n="databaseTitle">Database</h4>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
      <i class="bi bi-plus-circle"></i> <span data-i18n="addNewUser">Add new user</span>
    </button>
  </div>
  <div class="card">
    <div class="card-header" data-i18n="usersTable">Users Table</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-dark table-hover mb-0">
          <thead>
            <tr>
              <th>ID</th>
              <th data-i18n="name">Name</th>
              <th data-i18n="email">Email</th>
              <th data-i18n="role">Role</th>
              <th data-i18n="status">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $rs = $conn->query("SELECT * FROM usuarios");
            while ($u = $rs->fetch_assoc()) {
              echo "<tr>
              <td>{$u['id']}</td>
              <td>{$u['nombre_completo']}</td>
              <td>{$u['correo']}</td>
              <td>{$u['rol']}</td>
              <td>Active</span></td>
            </tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="card mt-5">
  <div class="card-header" data-i18n="filesTable">Files Table</div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-dark table-hover mb-0">
        <thead>
          <tr>
            <th>ID</th>
            <th data-i18n="filename">Filename</th>
            <th data-i18n="type">Type</th>
            <th data-i18n="size">Size</th>
            <th data-i18n="uploadedBy">Uploaded By</th>
            <th data-i18n="date">Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $rs = $conn->query("SELECT a.*, u.nombre_completo FROM archivos a LEFT JOIN usuarios u ON a.usuario_id = u.id ORDER BY a.fecha_subida DESC");
          while ($f = $rs->fetch_assoc()) {
            echo "<tr>
              <td>{$f['id']}</td>
              <td>{$f['nombre_archivo']}</td>
              <td>{$f['tipo_archivo']}</td>
              <td>" . round($f['tamano'] / 1024, 1) . " KB</td>
              <td>{$f['nombre_completo']}</td>
              <td>" . date('d/m/Y H:i', strtotime($f['fecha_subida'])) . "</td>
              <td>
                <a href='./{$f['ruta_archivo']}' download><i class='bi bi-download'></i></a>
              </td>
            </tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</div>

<!-- Modal de agregar usuario -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="add_user.php">
      <div class="modal-header">
        <h5 class="modal-title" id="addUserModalLabel">Add new user</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2">
          <label class="form-label">Full Name</label>
          <input type="text" name="nombre_completo" class="form-control" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Email</label>
          <input type="email" name="correo" class="form-control" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Role</label>
          <select name="rol" class="form-select">
            <option value="admin">Administrator</option>
            <option value="auditor">Auditor</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Register</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="confirmDeleteFileModal" tabindex="-1" aria-labelledby="confirmDeleteFileLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteFileLabel" data-i18n="delete">Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <span data-i18n="confirmDelete">Are you sure you want to delete this file?</span>
        <div id="deleteFileName" class="fw-bold text-danger mt-2"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-i18n="cancel">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn" data-i18n="confirm">Confirm</button>
      </div>
    </div>
  </div>
</div>




<script>
  function goToUploadTab() {
    document.querySelector('[data-tab="upload"]').click();
  }




</script>