<?php
session_start();
if (isset($_SESSION['usuario'])) {
    header('Location: dashboard.php');
    exit;
}
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = ? AND password = ?");
    $stmt->bind_param("ss", $correo, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if ($usuario) {
        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nombre' => $usuario['nombre_completo'],
            'correo' => $usuario['correo'],
            'rol' => $usuario['rol']
        ];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Login - Rheonics Web Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
      body {
        background: #1E1E2F;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .login-card {
        max-width: 400px;
        width: 100%;
        background: #222831;
        color: #eee;
        border-radius: 1rem;
        box-shadow: 0 4px 24px rgba(0,0,0,0.19);
        padding: 2rem 2.3rem;
      }
      .form-label { color: #E5E5E5; }
      .btn-primary { background: #00ADB5; border: none; }
      .btn-primary:hover { background: #048691; }
      .logo {
        display: block;
        margin: 0 auto 1.5rem;
        max-height: 52px;
      }
    </style>
</head>
<body>
  <div class="login-card">
      <img src="img/RHEONICS WHITE LOGO.png" alt="Logo" class="logo">
      <h4 class="mb-3 text-center">Iniciar sesión</h4>
      <?php if ($error): ?>
          <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <form method="POST" autocomplete="off">
          <div class="mb-3">
              <label for="correo" class="form-label">Correo</label>
              <input type="email" class="form-control" id="correo" name="correo" required autofocus>
          </div>
          <div class="mb-3">
              <label for="password" class="form-label">Contraseña</label>
              <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Entrar</button>
      </form>
      <div class="text-center mt-3 small text-secondary">
          <span>Demo: admin1@demo.com / admin2@demo.com <br> Contraseña: admin123</span>
      </div>
  </div>
</body>
</html>
