<?php
require '../config/config.php';

// Função para limpar dados de teste
function cleanupTestData($pdo) {
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email = 'test@example.com');
        $stmt->execute();
        echo "Test cleanup: Test data removed.<br>";
        return true;
    } catch (Exception $e) {
        echo "Test cleanup failed: " . $e->getMessage() . "<br>";
        return false;
    }
}

// Executa a limpeza
cleanupTestData($pdo);
