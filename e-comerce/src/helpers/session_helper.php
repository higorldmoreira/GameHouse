<?php

// Inicializa a sessão apenas se ela ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

/**
 * Função para verificar se o usuário está logado.
 *
 * @return bool Retorna true se o usuário estiver logado, false caso contrário.
 */
function isLoggedIn(): bool
{
  return isset($_SESSION['user_id']);
}

/**
 * Função para verificar se o usuário tem o papel de administrador.
 *
 * @return bool Retorna true se o usuário for admin, false caso contrário.
 */
function isAdmin(): bool
{
  return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Função para redirecionar o usuário se não estiver logado.
 *
 * @param string $redirectUrl URL para redirecionar o usuário caso não esteja logado.
 */
function requireLogin(string $redirectUrl = '../views/Login.php')
{
  if (!isLoggedIn()) {
    header("Location: $redirectUrl?error=You must be logged in to access this page.");
    exit();
  }
}

/**
 * Função para redirecionar o usuário se ele não for admin.
 *
 * @param string $redirectUrl URL para redirecionar o usuário caso não seja admin.
 */
function requireAdmin(string $redirectUrl = '../views/HomePage.php')
{
  if (!isAdmin()) {
    header("Location: $redirectUrl?error=You do not have permission to access this page.");
    exit();
  }
}
