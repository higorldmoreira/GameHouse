<?php

// Inicializa a sessão se ainda não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclui as configurações do banco de dados
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitiza e valida os dados de entrada
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    try {
        // Verifica se o e-mail existe no banco de dados
        $stmt = $pdo->prepare("SELECT id, full_name, role, password FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se o usuário foi encontrado e a senha está correta
        if ($user && password_verify($password, $user['password'])) {
            // Define as variáveis de sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_name'] = $user['full_name'];

            // Redireciona com base no papel do usuário
            $redirectUrl = $user['role'] === 'admin' ? '../views/AdminDashboard.php' : '../views/HomePage.php';
            header("Location: $redirectUrl");
            exit();
        } else {
            // Usuário não encontrado ou senha incorreta
            header('Location: ../views/Login.php?error=Incorrect email or password.');
            exit();
        }
    } catch (Exception $e) {
        // Registra o erro no log do servidor
        error_log("Erro ao processar o login: " . $e->getMessage());

        // Redireciona para a página de login com uma mensagem genérica
        header('Location: ../views/Login.php?error=Error processing login. Please try again later.');
        exit();
    }
}
