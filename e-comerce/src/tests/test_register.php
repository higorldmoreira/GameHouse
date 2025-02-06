<?php
require '../config/config.php';

// Função de teste para criar um novo usuário
function testRegister($pdo, $full_name, $email, $password, $confirm_password) {
    if ($password !== $confirm_password) {
        echo "Test failed: Passwords do not match.<br>";
        return false;
    }

    try {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password) VALUES (:full_name, :email, :password)");
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->execute();
        echo "Test passed: User registered successfully.<br>";
        return true;
    } catch (Exception $e) {
        echo "Test failed: " . $e->getMessage() . "<br>";
        return false;
    }
}

// Teste de registro
testRegister($pdo, 'Test User', 'test@example.com', 'password123', 'password123');
