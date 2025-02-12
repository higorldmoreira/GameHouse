<?php
// Inclui os arquivos necessários
require_once __DIR__ . '/../helpers/session_helper.php';
require_once __DIR__ . '/../config/config.php';

// Verifica se o usuário é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../views/Login.php?error=Restricted access to administrators.');
  exit();
}

// Busca todos os produtos no banco de dados
try {
  $stmt = $pdo->query("SELECT * FROM games");
  $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  error_log("Error searching for products: " . $e->getMessage());
  $products = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin</title>
  <!--Tailwind CSS-->
  <script src="https://cdn.tailwindcss.com"></script>
  <!--fonts-->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <!--global css-->
  <link rel="stylesheet" href="./src/assets/css/global.css">
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">
  <header class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold">Admin Dashboard</h1>
    <nav class="flex space-x-4">
      <a href="HomePage.php" class="text-gray-700 hover:text-blue-500">Home</a>
      <form action="../controllers/LogoutController.php" method="POST">
        <button type="submit" class="text-red-500 hover:underline">Exit</button>
      </form>
    </nav>
  </header>

  <main class="container mx-auto px-6 py-8">
    <!-- Adicionar Novo Produto -->
    <section class="bg-white p-6 shadow-md rounded-lg mb-8">
      <h2 class="text-xl font-bold mb-4">Add New Product</h2>
      <form action="../controllers/ProductController.php" method="POST" enctype="multipart/form-data" class="space-y-4">
        <input type="hidden" name="action" value="add">
        <div>
          <label for="name" class="block text-gray-700">Product Name</label>
          <input type="text" id="name" name="name" required class="w-full px-4 py-2 border rounded-md">
        </div>
        <div>
          <label for="producer" class="block text-gray-700">Producer</label>
          <input type="text" id="producer" name="producer" required class="w-full px-4 py-2 border rounded-md">
        </div>
        <div>
          <label for="genre" class="block text-gray-700">Genre</label>
          <input type="text" id="genre" name="genre" required class="w-full px-4 py-2 border rounded-md">
        </div>
        <div>
          <label for="price" class="block text-gray-700">Price</label>
          <input type="number" step="0.01" id="price" name="price" required class="w-full px-4 py-2 border rounded-md">
        </div>
        <div>
          <label for="stock" class="block text-gray-700">Quantity in Stock</label>
          <input type="number" id="stock" name="stock" required class="w-full px-4 py-2 border rounded-md">
        </div>
        <div>
          <label for="image" class="block text-gray-700">Product Image</label>
          <input type="file" id="image" name="image" accept="image/*" required class="w-full px-4 py-2 border rounded-md">
        </div>
        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600">Add Product</button>
      </form>
    </section>

    <!-- Lista de Produtos -->
    <section class="bg-white p-6 shadow-md rounded-lg">
      <h2 class="text-xl font-bold mb-4">Registered Products</h2>
      <?php if (!empty($products)): ?>
        <table class="w-full border-collapse border border-gray-300">
          <thead>
            <tr class="bg-gray-200">
              <th class="border border-gray-300 px-4 py-2">ID</th>
              <th class="border border-gray-300 px-4 py-2">Name</th>
              <th class="border border-gray-300 px-4 py-2">Producer</th>
              <th class="border border-gray-300 px-4 py-2">Gender</th>
              <th class="border border-gray-300 px-4 py-2">Price</th>
              <th class="border border-gray-300 px-4 py-2">Stock</th>
              <th class="border border-gray-300 px-4 py-2">Image</th>
              <th class="border border-gray-300 px-4 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $product): ?>
              <tr>
                <td class="border border-gray-300 px-4 py-2"> <?= htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8') ?> </td>
                <td class="border border-gray-300 px-4 py-2"> <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?> </td>
                <td class="border border-gray-300 px-4 py-2"> <?= htmlspecialchars($product['producer'], ENT_QUOTES, 'UTF-8') ?> </td>
                <td class="border border-gray-300 px-4 py-2"> <?= htmlspecialchars($product['genre'], ENT_QUOTES, 'UTF-8') ?> </td>
                <td class="border border-gray-300 px-4 py-2"> AU$ <?= number_format($product['price'], 2, ',', '.') ?> </td>
                <td class="border border-gray-300 px-4 py-2"> <?= htmlspecialchars($product['stock'], ENT_QUOTES, 'UTF-8') ?> </td>
                <td class="border border-gray-300 px-4 py-2"><img src="../uploads/<?= htmlspecialchars($product['image_url'], ENT_QUOTES, 'UTF-8') ?>" alt="Produto" class="w-20"></td>
                <td class="border border-gray-300 px-4 py-2">
                  <a href="EditProduct.php?id=<?= htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8') ?>" class="text-blue-500 hover:underline">Edit</a>
                  <a href="../controllers/ProductController.php?action=delete&id=<?= htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8') ?>" onclick="return confirm('Deseja excluir este produto?');" class="text-red-500 hover:underline">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="text-gray-500">No products found.</p>
      <?php endif; ?>
    </section>
  </main>
</body>

</html>