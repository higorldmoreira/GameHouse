<?php
// Inclui o helper e o arquivo de configuração
require_once __DIR__ . '/../helpers/session_helper.php';
require_once __DIR__ . '/../config/config.php';

// Validação do ID do produto
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
  header('Location: ../views/AdminDashboard.php?error=ID do produto inválido.');
  exit();
}

// Busca o produto no banco de dados
try {
  $stmt = $pdo->prepare("SELECT * FROM games WHERE id = :id");
  $stmt->bindParam(':id', $id, PDO::PARAM_INT);
  $stmt->execute();
  $product = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$product) {
    header('Location: ../views/AdminDashboard.php?error=Produto não encontrado.');
    exit();
  }
} catch (Exception $e) {
  error_log("Erro ao buscar o produto: " . $e->getMessage());
  header('Location: ../views/AdminDashboard.php?error=Erro ao buscar o produto.');
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome to GH Store</title>
  <!--Tailwind CSS-->
  <script src="https://cdn.tailwindcss.com"></script>
  <!--fonts-->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <!--global css-->
  <link rel="stylesheet" href="./src/assets/css/global.css">
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 shadow-lg rounded-lg w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center">Editar Produto</h2>
    <form action="../controllers/ProductController.php" method="POST" enctype="multipart/form-data" class="space-y-4">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" value="<?= htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8') ?>">

      <div>
        <label for="name" class="block text-gray-700">Nome do Produto</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>" required
          class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
      </div>

      <div>
        <label for="producer" class="block text-gray-700">Produtora</label>
        <input type="text" id="producer" name="producer" value="<?= htmlspecialchars($product['producer'], ENT_QUOTES, 'UTF-8') ?>" required
          class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
      </div>

      <div>
        <label for="genre" class="block text-gray-700">Gênero</label>
        <input type="text" id="genre" name="genre" value="<?= htmlspecialchars($product['genre'], ENT_QUOTES, 'UTF-8') ?>" required
          class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
      </div>

      <div>
        <label for="price" class="block text-gray-700">Preço</label>
        <input type="number" step="0.01" id="price" name="price" value="<?= htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8') ?>" required
          class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
      </div>

      <div>
        <label for="stock" class="block text-gray-700">Quantidade no Estoque</label>
        <input type="number" id="stock" name="stock" value="<?= htmlspecialchars($product['stock'], ENT_QUOTES, 'UTF-8') ?>" required
          class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
      </div>

      <div>
        <label for="image" class="block text-gray-700">Imagem do Produto (opcional)</label>
        <input type="file" id="image" name="image" accept="image/*"
          class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
      </div>

      <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600">
        Salvar Alterações
      </button>
    </form>
  </div>
</body>

</html>