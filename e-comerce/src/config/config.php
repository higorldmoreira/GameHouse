<?php
// Inicia a sessão apenas se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurações do banco de dados usando constantes para facilitar mudanças
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecommerce_games_db');
define('DB_USER', 'root');
define('DB_PASSWORD', '220314');

try {
    // Cria a conexão com o banco de dados
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lança exceções em caso de erros
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Define o modo de fetch padrão
            PDO::ATTR_PERSISTENT => false, // Desativa conexões persistentes para evitar problemas de performance
        ]
    );
} catch (PDOException $e) {
    // Registra o erro em um arquivo de log específico para o banco
    error_log("Error connecting to database: " . $e->getMessage(), 0);

    // Exibe uma mensagem genérica para o usuário final
    die("Error connecting to database. Please try again later.");
}
