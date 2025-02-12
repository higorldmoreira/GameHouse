<?php

namespace App\Services;

use Exception;
use PDO;

class CartService
{
  private $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  /**
   * Adiciona um item ao carrinho.
   */
  public function addItem(int $productId, int $quantity): string
  {
    try {
      // Verifica se o produto existe e tem estoque suficiente
      $stmt = $this->pdo->prepare("SELECT stock FROM games WHERE id = :id");
      $stmt->execute([':id' => $productId]);
      $product = $stmt->fetch();

      if (!$product) {
        return "Erro: Produto não encontrado.";
      }

      if ($product['stock'] < $quantity) {
        return "Erro: Estoque insuficiente.";
      }

      // Adiciona o item ao carrinho (sessão)
      if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
      }

      if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
      } else {
        $_SESSION['cart'][$productId] = $quantity;
      }

      return "Item adicionado ao carrinho com sucesso!";
    } catch (Exception $e) {
      error_log("Erro ao adicionar item ao carrinho: " . $e->getMessage());
      return "Erro ao adicionar item: " . $e->getMessage();
    }
  }

  /**
   * Obtém o total do carrinho de compras.
   */
  public function getCartTotal(): float
  {
    $total = 0.0;

    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
      foreach ($_SESSION['cart'] as $productId => $quantity) {
        // Busca o preço do produto no banco de dados
        $stmt = $this->pdo->prepare("SELECT price FROM games WHERE id = :id");
        $stmt->execute([':id' => $productId]);
        $product = $stmt->fetch();

        if ($product) {
          $total += $product['price'] * $quantity;
        }
      }
    }

    return $total;
  }

  /**
   * Atualiza o estoque dos produtos comprados.
   */
  public function updateStockAfterPurchase(): string
  {
    try {
      if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return "Erro: Carrinho vazio.";
      }

      foreach ($_SESSION['cart'] as $productId => $quantity) {
        $stmt = $this->pdo->prepare("UPDATE games SET stock = stock - :quantity WHERE id = :id AND stock >= :quantity");
        $stmt->execute([
          ':id' => $productId,
          ':quantity' => $quantity
        ]);

        if ($stmt->rowCount() === 0) {
          return "Erro: Estoque insuficiente para um dos itens.";
        }
      }

      return "Estoque atualizado com sucesso.";
    } catch (Exception $e) {
      error_log("Erro ao atualizar estoque: " . $e->getMessage());
      return "Erro ao atualizar estoque.";
    }
  }
}
