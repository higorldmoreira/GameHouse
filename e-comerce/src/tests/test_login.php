<?php
require '../config/config.php';

// Função de teste para login
function testLogin($pdo, $email, $password) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            echo "Test passed: Login successful.<br>";
            return true;
        } else {
            echo "Test failed: Invalid email or password.<br>";
            return false;
        }
    } catch (Exception $e) {
        echo "Test failed: " . $e->getMessage() . "<br>";
        return false;
    }
}

// Teste de login
testLogin($pdo, 'test@example.com', 'password123');
testLogin($pdo, 'wrong@example.com', 'password123'); // Deve falhar
testLogin($pdo, 'test@example.com', 'wrongpassword'); // Deve falhar
