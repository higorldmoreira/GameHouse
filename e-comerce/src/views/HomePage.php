<?php
// Inclui o arquivo de inicialização da sessão e configuração
require_once __DIR__ . '/../helpers/session_helper.php'; // Caminho corrigido para o helper
require_once __DIR__ . '/../config/config.php'; // Caminho corrigido para o config

$isLoggedIn = isset($_SESSION['user_id']); // Verifica se o usuário está logado

// Consulta produtos cadastrados com filtros
try {
  $query = "SELECT * FROM games";
  $params = [];

  if (isset($_GET['filter'])) {
    if ($_GET['filter'] === 'price') {
      $query .= " ORDER BY price ASC";
    } elseif ($_GET['filter'] === 'genre' && !empty($_GET['genre'])) {
      $query .= " WHERE genre = :genre";
      $params[':genre'] = filter_input(INPUT_GET, 'genre', FILTER_SANITIZE_STRING);
    }
  }

  $stmt = $pdo->prepare($query);
  $stmt->execute($params);
  $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  error_log("Erro ao buscar produtos: " . $e->getMessage());
  $products = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GH Store</title>
  <!--Tailwind CSS-->
  <script src="https://cdn.tailwindcss.com"></script>
  <!--fonts-->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <!--global css-->
  <link rel="stylesheet" href="./src/assets/css/global.css">
</head>


<body class="bg-[#f8f9fa] text-gray-900 h-screen flex flex-col">
  <header class="bg-white shadow-md py-4 fixed w-full top-0 z-50">
    <div class="container mx-auto flex justify-between items-center px-6">
      <h1 class="text-xl font-bold">Game Store</h1>
      <nav class="hidden md:flex space-x-6">
        <a href="HomePage.php" class="text-gray-700 hover:text-blue-500">Home</a>
        <a href="CartPage.php" class="text-gray-700 hover:text-blue-500">Cart</a>
        <?php if ($isLoggedIn): ?>
          <form action="../controllers/LogoutController.php" method="POST">
            <button type="submit" class="text-blue-500 hover:underline">Exit</button>
          </form>
        <?php else: ?>
          <a href="Login.php" class="text-blue-500 hover:underline">Login</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main class="pt-20 flex-grow container mx-auto px-6">
    <!-- Filtro -->
    <section class="mb-8">
      <form method="GET" action="HomePage.php" class="flex flex-wrap gap-4 items-center">
        <label for="filter" class="text-gray-700">Filter by:</label>
        <select name="filter" id="filter" class="border px-3 py-2 rounded-md">
          <option value="">Select</option>
          <option value="price">Price</option>
          <option value="genre">Gender</option>
        </select>
        <input type="text" name="genre" placeholder="Digite o gênero" id="genreInput" class="border px-3 py-2 rounded-md" value="<?= htmlspecialchars($_GET['genre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Filter</button>
      </form>
    </section>

    <!-- Lista de Produtos -->
    <section class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
          <div class="bg-white shadow-md rounded-lg p-4 flex flex-col items-center text-center">
            <img src="../uploads/<?= htmlspecialchars($product['image_url'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>" class="w-40 h-auto">
            <h3 class="text-lg font-bold mt-2"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
            <p class="text-gray-600">Gender: <?= htmlspecialchars($product['genre'], ENT_QUOTES, 'UTF-8') ?></p>
            <p class="text-gray-600">Producer: <?= htmlspecialchars($product['producer'], ENT_QUOTES, 'UTF-8') ?></p>
            <p class="text-blue-500 font-bold">AU$ <?= number_format($product['price'], 2, ',', '.') ?></p>
            <p class="text-gray-500">Stock: <?= htmlspecialchars($product['stock'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php if ($isLoggedIn): ?>
              <form action="../controllers/CartController.php" method="POST" class="mt-4">
                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8') ?>">
                <label for="quantity" class="text-gray-700">Amount:</label>
                <input type="number" name="quantity" min="1" max="<?= $product['stock'] ?>" required class="border px-2 py-1 rounded-md">
                <button type="submit" name="action" value="add" class="mt-2 bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Add to Cart</button>
              </form>
            <?php else: ?>
              <p class="text-red-500 mt-2">You need <a href="Login.php" class="text-blue-500 underline">log in</a> to buy.</p>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-center text-gray-500 col-span-full">No products found.</p>
      <?php endif; ?>
    </section>
  </main>
</body>

</html>