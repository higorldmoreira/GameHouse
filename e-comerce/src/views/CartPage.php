<?php

namespace App\Views;

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../services/CartService.php';

use App\Services\CartService;
use PDO;

// Verifica se o PDO global está configurado corretamente
global $pdo;
if (!isset($pdo) || !$pdo instanceof PDO) {
  die("Error: Database connection not configured correctly.");
}

$cartService = new CartService($pdo);
$cartItems = $_SESSION['cart'] ?? [];
$total = $cartService->getCartTotal();

if (!isset($_SESSION['cart'])) {
  error_log("The cart session is empty.");
} else {
  error_log("Cart Contents: " . print_r($_SESSION['cart'], true));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Carrinho de Compras</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
  <div class="container mx-auto p-8">
    <h1 class="text-2xl font-bold mb-6">Shopping cart</h1>

    <?php if (empty($cartItems)): ?>
      <p class="text-gray-600">Your cart is empty.</p>
      <a href="HomePage.php" class="text-blue-500 hover:underline">Keep shopping</a>
    <?php else: ?>
      <div class="bg-white shadow rounded p-6 mb-6">
        <h2 class="text-lg font-semibold">Items in Cart</h2>
        <ul class="mt-4">
          <?php foreach ($cartItems as $productId => $quantity): ?>
            <?php
            // Alterado para buscar na tabela games
            $stmt = $pdo->prepare("SELECT name, price FROM games WHERE id = :id");
            $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            <li class="flex justify-between items-center py-2 border-b">
              <span><?= htmlspecialchars($product['name'] ?? 'Jogo não encontrado') ?></span>
              <span><?= $quantity ?> x R$<?= number_format($product['price'] ?? 0, 2, ',', '.') ?></span>
              <form action="../controllers/CartController.php" method="POST" class="inline-block">
                <input type="hidden" name="action" value="remove">
                <input type="hidden" name="product_id" value="<?= $productId ?>">
                <button type="submit" class="text-red-500 hover:underline">Remove</button>
              </form>
            </li>
          <?php endforeach; ?>
        </ul>

        <div class="mt-4 text-right">
          <p class="text-lg font-bold">Total: AU$<?= number_format($total, 2, ',', '.') ?></p>
        </div>
      </div>

      <a href="Checkout.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Checkout</a>
    <?php endif; ?>
  </div>
</body>

</html>