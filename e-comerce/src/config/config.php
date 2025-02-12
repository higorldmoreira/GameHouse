<?php
// Inicia a sessão apenas se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurações do banco de dados
define('DB_HOST', 'localhost'); // Se estiver usando XAMPP, geralmente é 'localhost'
define('DB_NAME', 'ecommerce_games_db'); // Verifique se esse é o nome correto do banco
define('DB_USER', 'root'); // Usuário padrão do MySQL no XAMPP
define('DB_PASSWORD', '220314'); // Senha do banco de dados (ajuste se necessário)

try {
    // Criando a conexão com o banco de dados
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lança exceções em caso de erro
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Retorna os resultados como array associativo
            PDO::ATTR_PERSISTENT => false, // Evita conexões persistentes para prevenir erros
        ]
    );

    // Teste de conexão
    $pdo->query("SELECT 1");
} catch (PDOException $e) {
    // Registra o erro no log do sistema
    error_log("Error connecting to database: " . $e->getMessage(), 0);

    // Exibe uma mensagem genérica para evitar vazamento de informações sensíveis
    die("Error connecting to database. Please try again later.");
}

// Tornando a variável $pdo global para ser acessada em outros arquivos
global $pdo;
