<?php

require_once __DIR__ . '/src/helpers/session_helper.php'; // Caminho correto para o helper

require_once __DIR__ . '/src/config/config.php';

$isLoggedIn = isset($_SESSION['user_id']);
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

<body>

  <header class="bg-white shadow-md py-4 fixed w-full top-0 z-50">
    <div class="container mx-auto flex justify-between items-center px-6">
      <img class="h-10" src="./src/assets/images/logo.svg" alt="Logo GH Store">
      <button id="menu-toggle" class="md:hidden focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
        </svg>
      </button>
      <nav id="menu" class="hidden md:flex space-x-6">
        <a href="src/views/HomePage.php" class="text-gray-700 hover:text-blue-500">Home</a>
        <a href="src/views/CartPage.php" class="text-gray-700 hover:text-blue-500">Cart</a>
        <?php if ($isLoggedIn): ?>
          <form action="src/controllers/LogoutController.php" method="POST">
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">Exit</button>
          </form>
        <?php else: ?>
          <a href="src/views/Login.php" class="text-gray-700 hover:text-blue-500">Login</a>
          <a href="src/views/Register.php" class="text-gray-700 hover:text-blue-500">Register</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main class="flex-grow flex items-center ">
    <section id="hero" class="container max-w-4/5 w-full md:w-4/5 mx-auto flex flex-col md:flex-row items-center justify-center py-16 text-center md:text-left">
      <div class="flex items-center justify-center flex-col items-center md:items-start">
        <img class="w-64" src="./src/assets/images/logo.svg" alt="Logo GH Store">
        <h2 class="text-3xl font-bold">Welcome to the GH Store</h2>
        <p class="mt-4 text-gray-600">Here you will find the best games at incredible prices.</p>
        <p class="mt-2 text-gray-600">Explore our collection and choose the best games for you!</p>
        <a href="src/views/HomePage.php" class="mt-6 inline-block bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600">Explore Products</a>
      </div>
    </section>
  </main>

  <footer class="bg-white py-4 text-center text-gray-700 mt-8">
    <p>&copy; <?= date('Y') ?>GH Store. All rights reserved.</p>
  </footer>

  <script>
    document.getElementById('menu-toggle').addEventListener('click', function() {
      document.getElementById('menu').classList.toggle('hidden');
    });
  </script>

</body>

</html>