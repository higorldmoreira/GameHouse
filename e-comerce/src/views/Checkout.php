<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
  <div class="container mx-auto p-8">
    <h1 class="text-2xl font-bold mb-6">Checkout</h1>

    <!-- Exibe o total do carrinho -->
    <div class="bg-white shadow rounded p-6 mb-6">
      <h2 class="text-lg font-semibold">Order Summary</h2>
      <p class="mt-4">Total: <span id="cart-total" class="font-bold text-lg">AU$<?= number_format($_SESSION['cart_total'] ?? 0, 2, ',', '.') ?></span></p>
    </div>

    <!-- Formulário de pagamento -->
    <form action="../controllers/CartController.php" method="POST" class="bg-white shadow rounded p-6">
      <input type="hidden" name="action" value="finalize">

      <h2 class="text-lg font-semibold mb-4">Choose Payment Method</h2>

      <!-- Cartão de Crédito -->
      <div class="mb-4">
        <label class="flex items-center">
          <input type="radio" name="payment_method" value="credit_card" class="mr-2" required>
          <span>Credit card</span>
        </label>
        <div id="credit-card-form" class="hidden mt-4">
          <label class="block mb-2">
            Card number:
            <input type="text" name="card_number" class="w-full p-2 border rounded" maxlength="16">
          </label>
          <label class="block mb-2">
            Expiration Date (MM/YY):
            <input type="text" name="expiry_date" class="w-full p-2 border rounded" maxlength="5">
          </label>
          <label class="block mb-2">
            Security Code (CVV):
            <input type="text" name="cvv" class="w-full p-2 border rounded" maxlength="3">
          </label>
        </div>
      </div>

      <!-- PayPal
      <div class="mb-4">
        <label class="flex items-center">
          <input type="radio" name="payment_method" value="paypal" class="mr-2" required>
          <span>PayPal</span>
        </label>
      </div>
       -->

      <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        Checkout
      </button>
    </form>
  </div>

  <script>
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
      radio.addEventListener('change', function() {
        const creditCardForm = document.getElementById('credit-card-form');
        if (this.value === 'credit_card') {
          creditCardForm.classList.remove('hidden');
        } else {
          creditCardForm.classList.add('hidden');
        }
      });
    });
  </script>
</body>

</html>