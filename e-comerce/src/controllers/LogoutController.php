<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start(); // Garante que a sessão seja inicializada
}

require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
  $password = $_POST['password'];

  try {
    // Busca o usuário pelo e-mail
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o usuário foi encontrado
    if (!$user) {
      header('Location: ../views/Login.php?error=Incorrect email or password.');
      exit();
    }

    // Log para depuração do usuário encontrado (apenas para testes)
    error_log("User found: " . print_r($user, true));

    // Verifica se a senha é válida
    if (!password_verify($password, $user['password'])) {
      header('Location: ../views/Login.php?error=Incorrect email or password.');
      exit();
    }

    // Login bem-sucedido: Configura as variáveis de sessão
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['user_name'] = $user['full_name'];

    // Log para verificar a sessão (apenas para testes)
    error_log("Session configured: " . print_r($_SESSION, true));

    // Redireciona com base no papel do usuário
    if ($_SESSION['role'] === 'admin') {
      header('Location: ../views/AdminDashboard.php');
    } else {
      header('Location: ../views/HomePage.php');
    }
    exit();
  } catch (Exception $e) {
    // Loga o erro
    error_log("Erro ao processar o login: " . $e->getMessage());
    header('Location: ../views/Login.php?error=Error processing login.');
    exit();
  }
}
