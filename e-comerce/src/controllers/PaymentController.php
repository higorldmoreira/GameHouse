<?php

namespace App\Controllers;

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . '/../config/config.php';

use PDO;
use Exception;

class PaymentController
{
  private $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  /**
   * Processa o pagamento via cartão de crédito e atualiza o estoque.
   */
  public function processCreditCardPayment(array $paymentData): string
  {
    try {
      $paymentAmount = $_SESSION['cart_total'] ?? 0;
      if ($paymentAmount <= 0) {
        throw new Exception("Erro: Total purchase amount is invalid.");
      }

      if (empty($paymentData['card_number']) || empty($paymentData['cvv']) || empty($paymentData['expiry_date'])) {
        throw new Exception("Incomplete card details.");
      }

      // Simulação de pagamento bem-sucedido
      $isPaymentSuccessful = true;

      if (!$isPaymentSuccessful) {
        throw new Exception("Card payment processing failure.");
      }

      $this->pdo->beginTransaction(); // Inicia a transação

      // Criar um pedido na tabela transactions
      $stmt = $this->pdo->prepare("INSERT INTO transactions (user_id, order_id, amount, payment_method, status) VALUES (:user_id, :order_id, :amount, :payment_method, :status)");

      $orderId = rand(1000, 9999);

      $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':order_id' => $orderId,
        ':amount' => $paymentAmount,
        ':payment_method' => 'credit_card',
        ':status' => 'completed',
      ]);

      $this->updateStockAfterPurchase();

      $this->pdo->commit(); // Confirma a transação

      // Esvazia o carrinho após a compra ser finalizada com sucesso
      unset($_SESSION['cart']);

      return "Payment by credit card completed successfully!";
    } catch (Exception $e) {
      $this->pdo->rollBack(); // Reverte a transação em caso de erro
      error_log("Card payment error: " . $e->getMessage());
      return "Error processing payment: " . $e->getMessage();
    }
  }

  /**
   * Processa o pagamento via PayPal e retorna a URL de pagamento.
   */
  public function processPayPalPayment(): string
  {
    try {
      $paymentAmount = $_SESSION['cart_total'] ?? 0;
      if ($paymentAmount <= 0) {
        throw new Exception("Error: Total purchase amount is invalid.");
      }

      $orderId = rand(1000, 9999);

      $stmt = $this->pdo->prepare("INSERT INTO transactions (user_id, order_id, amount, payment_method, status) VALUES (:user_id, :order_id, :amount, :payment_method, :status)");
      $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':order_id' => $orderId,
        ':amount' => $paymentAmount,
        ':payment_method' => 'paypal',
        ':status' => 'pending',
      ]);

      // URL para ambiente de testes do PayPal
      $paypalUrl = "https://www.sandbox.paypal.com/cgi-bin/webscr";

      $queryParams = http_build_query([
        'cmd' => '_xclick',
        'business' => 'higorldmoreira@gmail.com',
        'item_name' => 'Compra no E-Commerce',
        'amount' => $paymentAmount,
        'currency_code' => 'USD',
        'return' => "http://localhost/e-comerce/src/views/SuccessPage.php",
        'cancel_return' => "http://localhost/e-comerce/src/views/CartPage.php?error=Pagamento cancelado.",
        'notify_url' => "http://localhost/e-comerce/src/controllers/PaymentWebhook.php"
      ]);

      return "$paypalUrl?$queryParams";
    } catch (Exception $e) {
      error_log("Error paying via PayPal: " . $e->getMessage());
      return "Error processing payment via PayPal: " . $e->getMessage();
    }
  }

  /**
   * Atualiza o estoque dos produtos comprados.
   */
  public function updateStockAfterPurchase(): void
  {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
      throw new Exception("Error: Empty cart.");
    }

    foreach ($_SESSION['cart'] as $productId => $quantity) {
      $stmt = $this->pdo->prepare("UPDATE games SET stock = stock - :quantity WHERE id = :id AND stock >= :quantity");
      $stmt->execute([
        ':id' => $productId,
        ':quantity' => $quantity
      ]);

      if ($stmt->rowCount() === 0) {
        throw new Exception("Error: Insufficient stock for one of the items.");
      }
    }
  }
}
