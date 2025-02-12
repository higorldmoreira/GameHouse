<?php
require_once '../config/config.php';

// Verifica se uma ação foi enviada
$action = $_POST['action'] ?? $_GET['action'] ?? null;

if (!$action) {
  header('Location: ../views/AdminDashboard.php?error=Unspecified action.');
  exit();
}

// Executa a ação correspondente
switch ($action) {
  case 'add':
    addProduct($pdo);
    break;
  case 'edit':
    editProduct($pdo);
    break;
  case 'delete':
    deleteProduct($pdo);
    break;
  default:
    header('Location: ../views/AdminDashboard.php?error=Invalid action.');
    exit();
}

/**
 * Função para adicionar um produto.
 */
function addProduct($pdo)
{
  $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
  $producer = filter_input(INPUT_POST, 'producer', FILTER_SANITIZE_STRING);
  $genre = filter_input(INPUT_POST, 'genre', FILTER_SANITIZE_STRING);
  $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
  $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);

  if (!$name || !$producer || !$genre || !$price || !$stock) {
    header('Location: ../views/AdminDashboard.php?error=Invalid data.');
    exit();
  }

  // Validação e upload da imagem
  $imageName = handleImageUpload();
  if (!$imageName) {
    header('Location: ../views/AdminDashboard.php?error=Error uploading image.');
    exit();
  }

  try {
    $stmt = $pdo->prepare("INSERT INTO games (name, producer, genre, price, stock, image_url) VALUES (:name, :producer, :genre, :price, :stock, :image_url)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':producer', $producer);
    $stmt->bindParam(':genre', $genre);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':stock', $stock);
    $stmt->bindParam(':image_url', $imageName);
    $stmt->execute();

    header('Location: ../views/AdminDashboard.php?success=Product added successfully.');
  } catch (Exception $e) {
    error_log("Error adding product: " . $e->getMessage());
    header('Location: ../views/AdminDashboard.php?error=Error adding product.');
  }
  exit();
}

/**
 * Função para editar um produto.
 */
function editProduct($pdo)
{
  $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
  $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
  $producer = filter_input(INPUT_POST, 'producer', FILTER_SANITIZE_STRING);
  $genre = filter_input(INPUT_POST, 'genre', FILTER_SANITIZE_STRING);
  $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
  $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);

  if (!$id || !$name || !$producer || !$genre || !$price || !$stock) {
    header('Location: ../views/AdminDashboard.php?error=Invalid data.');
    exit();
  }

  try {
    $stmt = $pdo->prepare("UPDATE games SET name = :name, producer = :producer, genre = :genre, price = :price, stock = :stock WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':producer', $producer);
    $stmt->bindParam(':genre', $genre);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':stock', $stock);
    $stmt->execute();

    header('Location: ../views/AdminDashboard.php?success=Product updated successfully.');
  } catch (Exception $e) {
    error_log("Error editing product: " . $e->getMessage());
    header('Location: ../views/AdminDashboard.php?error=Error updating product.');
  }
  exit();
}

/**
 * Função para excluir um produto.
 */
function deleteProduct($pdo)
{
  $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

  if (!$id) {
    header('Location: ../views/AdminDashboard.php?error=Invalid ID.');
    exit();
  }

  try {
    $stmt = $pdo->prepare("DELETE FROM games WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    header('Location: ../views/AdminDashboard.php?success=Product deleted successfully.');
  } catch (Exception $e) {
    error_log("Error deleting product: " . $e->getMessage());
    header('Location: ../views/AdminDashboard.php?error=Error deleting product.');
  }
  exit();
}

/**
 * Função para validar e fazer upload de uma imagem.
 */
function handleImageUpload()
{
  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $imageTmpPath = $_FILES['image']['tmp_name'];
    $imageName = uniqid() . '-' . basename($_FILES['image']['name']);
    $uploadDir = '../uploads/';
    $destPath = $uploadDir . $imageName;

    if (move_uploaded_file($imageTmpPath, $destPath)) {
      return $imageName;
    }
  }
  return false;
}
