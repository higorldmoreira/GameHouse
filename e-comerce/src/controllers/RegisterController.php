<?php
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitização e validação dos dados enviados
    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!$full_name || !$email || !$password || !$confirm_password) {
        header('Location: ../views/Register.php?error=Please fill in all fields.');
        exit();
    }

    if ($password !== $confirm_password) {
        header('Location: ../views/Register.php?error=Passwords do not match.');
        exit();
    }

    try {
        // Verificar se o e-mail já está registrado
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $emailExists = $stmt->fetchColumn();

        if ($emailExists) {
            header('Location: ../views/Register.php?error=Email is already registered.');
            exit();
        }

        // Hash da senha
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Inserir o usuário no banco
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password) VALUES (:full_name, :email, :password)");
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->execute();

        // Redirecionar para a página de login com mensagem de sucesso
        header('Location: ../views/Login.php?success=Account created successfully.');
        exit();
    } catch (Exception $e) {
        // Registrar o erro no log do servidor
        error_log("Error registering user: " . $e->getMessage());
        header('Location: ../views/Register.php?error=Error creating account. Please try again later..');
        exit();
    }
}
