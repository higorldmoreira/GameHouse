<?php
require '../config/config.php';

try {
    $stmt = $pdo->query("SELECT 'Conexão bem-sucedida!' AS mensagem");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo $row['mensagem'];
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>