<?php

namespace App\Controllers;

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../services/CartService.php';
require_once __DIR__ . '/../controllers/PaymentController.php';

use App\Services\CartService;
use App\Controllers\PaymentController;
use PDO;

class CartController
{
  private $cartService;
  private $paymentController;

  public function __construct()
  {
    global $pdo;

    if (!$pdo instanceof PDO) {
      die("Erro: The database connection is not active.");
    }

    $this->cartService = new CartService($pdo);

    $this->paymentController = new PaymentController($pdo);
  }

  /**
   * Adiciona um item ao carrinho.
   */
  public function addItem()
  {
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

    if (!$productId || !$quantity) {
      header('Location: ../views/CartPage.php?error=Invalid data.');
      exit();
    }

    $message = $this->cartService->addItem($productId, $quantity);
    header("Location: ../views/CartPage.php?message=" . urlencode($message));
    exit();
  }

  /**
   * Remove um item do carrinho.
   */
  public function removeItem()
  {
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

    if (!$productId) {
      header('Location: ../views/CartPage.php?error=Invalid product ID.');
      exit();
    }

    if (isset($_SESSION['cart'][$productId])) {
      unset($_SESSION['cart'][$productId]);
    }

    header('Location: ../views/CartPage.php?message=Item removed successfully.');
    exit();
  }

  /**
   * Finaliza a compra.
   */
  public function finalizePurchase()
  {
    $total = $this->cartService->getCartTotal();

    if ($total <= 0) {
      header('Location: ../views/CartPage.php?error=Cart is empty.');
      exit();
    }

    $_SESSION['cart_total'] = $total;
    $paymentMethod = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_SPECIAL_CHARS);
    error_log("Payment method received: " . $paymentMethod);

    if ($paymentMethod === 'credit_card') {
      $response = $this->paymentController->processCreditCardPayment($_POST);
    } elseif ($paymentMethod === 'PayPal') {
      $paypalUrl = $this->paymentController->processPayPalPayment();
      header("Location: $paypalUrl");
      exit();
    } else {
      error_log("Invalid payment method: " . $paymentMethod);
      header('Location: ../views/CartPage.php?error=Invalid payment method.');
      exit();
    }

    // Se o pagamento for bem-sucedido, atualiza o estoque
    $stockUpdateMessage = $this->cartService->updateStockAfterPurchase();
    error_log($stockUpdateMessage);

    // Esvazia o carrinho após a compra ser finalizada com sucesso
    unset($_SESSION['cart']);

    header("Location: ../views/CartPage.php?message=" . urlencode("Purchase completed successfully! " . $stockUpdateMessage));
    exit();
  }
}

// Exemplo de uso
$cartController = new CartController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? null;

  switch ($action) {
    case 'add':
      $cartController->addItem();
      break;
    case 'remove':
      $cartController->removeItem();
      break;
    case 'finalize':
      $cartController->finalizePurchase();
      break;
    default:
      header('Location: ../views/HomePage.php?error=Ação inválida.');
      exit();
  }
}
