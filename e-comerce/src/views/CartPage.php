<?php
// Inclui o arquivo de inicialização da sessão e configuração
require_once __DIR__ . '/../helpers/session_helper.php'; // Caminho corrigido para o helper
require_once __DIR__ . '/../config/config.php'; // Caminho corrigido para o config

$isLoggedIn = isset($_SESSION['user_id']); // Verifica se o usuário está logado

// Certifique-se de que $cart está definido e carregado
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}
$cart = $_SESSION['cart'];

// Consulta produtos no carrinho
try {
  $placeholders = implode(',', array_fill(0, count($cart), '?'));
  $query = "SELECT * FROM games WHERE id IN ($placeholders)";
  $stmt = $pdo->prepare($query);
  $stmt->execute(array_keys($cart));
  $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  error_log("Erro ao buscar produtos no carrinho: " . $e->getMessage());
  $products = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cart</title>
  <!--Tailwind CSS-->
  <script src="https://cdn.tailwindcss.com"></script>
  <!--fonts-->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">
  <header class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold">Cart</h1>
    <nav>
      <a href="HomePage.php" class="text-blue-500 hover:underline">Home</a>
    </nav>
  </header>

  <main class="container mx-auto px-6 py-8">
    <section class="bg-white p-6 shadow-md rounded-lg">
      <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
          <div class="border-b border-gray-300 py-4 flex justify-between items-center">
            <div>
              <h3 class="text-lg font-bold"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
              <p class="text-gray-600">Amount: <?= htmlspecialchars($cart[$product['id']], ENT_QUOTES, 'UTF-8') ?></p>
              <p class="text-gray-600">Unit Price: AU$ <?= number_format($product['price'], 2, ',', '.') ?></p>
              <p class="text-gray-600">Subtotal: AU$ <?= number_format($product['price'] * $cart[$product['id']], 2, ',', '.') ?></p>
            </div>
            <form action="../controllers/CartController.php" method="POST">
              <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8') ?>">
              <button type="submit" name="action" value="remove" class="text-red-500 hover:underline">Remove</button>
            </form>
          </div>
        <?php endforeach; ?>

        <div class="text-right mt-6">
          <form action="../controllers/CartController.php" method="POST">
            <button type="submit" name="action" value="finalize" class="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600">
              Checkout
            </button>
          </form>
        </div>
      <?php else: ?>
        <p class="text-gray-500 text-center">Your cart is empty.</p>
      <?php endif; ?>
    </section>
  </main>
</body>

</html>