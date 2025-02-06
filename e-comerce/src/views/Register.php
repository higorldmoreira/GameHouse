<?php
// Inclui o arquivo de inicialização da sessão
require_once __DIR__ . '/../helpers/session_helper.php'; // Caminho correto para o helper

// Obtém mensagens de erro e sucesso, se existirem
$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!--Tailwind CSS-->
    <script src="https://cdn.tailwindcss.com"></script>
    <!--fonts-->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <!--global css-->
    <link rel="stylesheet" href="./src/assets/css/global.css">
</head>

<body class="bg-[#f8f9fa] flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 shadow-lg rounded-lg w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-6">
            <img src="../assets/images/logo.svg" alt="Logo GH Store" class="mx-auto w-24">
            <p class="text-gray-600 mt-2">Your favorite place to buy games</p>
        </div>

        <!-- Mensagens de Erro e Sucesso -->
        <?php if ($error): ?>
            <p class="text-red-500 text-center"> <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?> </p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="text-green-500 text-center"> <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?> </p>
        <?php endif; ?>

        <!-- Formulário de Registro -->
        <form action="../controllers/RegisterController.php" method="POST" class="space-y-4">
            <div>
                <label for="full_name" class="block text-gray-700">Full Name</label>
                <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required
                    class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="email" class="block text-gray-700">E-mail</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required
                    class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="password" class="block text-gray-700">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required
                    class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="confirm_password" class="block text-gray-700">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required
                    class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600">
                REGISTER
            </button>
        </form>
    </div>
</body>

</html>