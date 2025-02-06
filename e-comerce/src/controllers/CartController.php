<?php
if (session_status() === PHP_SESSION_NONE) {
  require_once '../helpers/session_helper.php'; // Corrige o caminho do helper
}

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
  header('Location: ../views/Login.php?error=Você precisa estar logado para acessar o carrinho.');
  exit();
}

// Inicializa o carrinho, se ainda não existir
if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

// Valida o método da requisição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  require '../config/config.php';

  $action = $_POST['action'] ?? null;
  $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
  $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

  // Verifica os parâmetros básicos
  if ($action !== 'finalize' && (!$productId || ($quantity !== false && $quantity <= 0))) {
    header('Location: ../views/HomePage.php?error=Dados inválidos.');
    exit();
  }

  switch ($action) {
    case 'add':
      addToCart($productId, $quantity, $pdo);
      break;

    case 'remove':
      removeFromCart($productId);
      break;

    case 'finalize':
      finalizePurchase($pdo);
      break;

    default:
      header('Location: ../views/HomePage.php?error=Ação inválida.');
      exit();
  }
}

/**
 * Adiciona um produto ao carrinho, verificando o estoque.
 */
function addToCart($productId, $quantity, $pdo)
{
  try {
    // Verifica se o produto existe e tem estoque suficiente
    $stmt = $pdo->prepare("SELECT stock FROM games WHERE id = :id");
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
      header('Location: ../views/HomePage.php?error=Produto não encontrado.');
      exit();
    }

    if ($product['stock'] < $quantity) {
      header('Location: ../views/HomePage.php?error=Estoque insuficiente para este produto.');
      exit();
    }

    // Adiciona ou atualiza o produto no carrinho
    $_SESSION['cart'][$productId] = ($_SESSION['cart'][$productId] ?? 0) + $quantity;

    header('Location: ../views/HomePage.php?success=Produto adicionado ao carrinho.');
    exit();
  } catch (Exception $e) {
    error_log("Erro ao adicionar ao carrinho: " . $e->getMessage());
    header('Location: ../views/HomePage.php?error=Erro ao adicionar ao carrinho.');
    exit();
  }
}

/**
 * Remove um produto do carrinho.
 */
function removeFromCart($productId)
{
  if (isset($_SESSION['cart'][$productId])) {
    unset($_SESSION['cart'][$productId]);
  }

  header('Location: ../views/CartPage.php?success=Produto removido do carrinho.');
  exit();
}

/**
 * Finaliza a compra e atualiza o estoque no banco de dados.
 */
function finalizePurchase($pdo)
{
  try {
    // Inicia uma transação para garantir consistência
    $pdo->beginTransaction();

    // Itera sobre os itens do carrinho
    foreach ($_SESSION['cart'] as $id => $qty) {
      // Verifica se o produto existe e tem estoque suficiente
      $stmt = $pdo->prepare("SELECT stock FROM games WHERE id = :id");
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      $product = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$product) {
        throw new Exception("Produto ID: $id não encontrado no banco de dados.");
      }

      if ($product['stock'] < $qty) {
        throw new Exception("Estoque insuficiente para o produto ID: $id. Estoque disponível: {$product['stock']}, solicitado: $qty.");
      }

      // Atualiza o estoque do produto
      $updateStmt = $pdo->prepare("UPDATE games SET stock = stock - :qty WHERE id = :id");
      $updateStmt->bindParam(':qty', $qty, PDO::PARAM_INT);
      $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);
      $updateStmt->execute();

      if ($updateStmt->rowCount() === 0) {
        throw new Exception("Falha ao atualizar estoque para o produto ID: $id.");
      }
    }

    // Limpa o carrinho após a finalização bem-sucedida
    $_SESSION['cart'] = [];

    // Confirma a transação
    $pdo->commit();

    header('Location: ../views/HomePage.php?success=Compra finalizada com sucesso.');
  } catch (Exception $e) {
    // Desfaz a transação em caso de erro
    $pdo->rollBack();

    // Loga o erro para depuração
    error_log("Erro ao finalizar compra: " . $e->getMessage());

    header('Location: ../views/HomePage.php?error=Erro ao finalizar a compra. Tente novamente.');
  }
  exit();
}
