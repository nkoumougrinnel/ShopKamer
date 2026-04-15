function formatCurrency(value) {
  return new Intl.NumberFormat("fr-FR", {
    style: "currency",
    currency: "XOF",
    maximumFractionDigits: 0,
  })
    .format(value)
    .replace("XOF", "FCFA");
}

function getCart() {
  const cart = localStorage.getItem("shopkamer_cart");
  return cart ? JSON.parse(cart) : [];
}

function setCart(cart) {
  localStorage.setItem("shopkamer_cart", JSON.stringify(cart));
}

function getCartTotal(cart) {
  return cart.reduce((total, item) => total + item.quantity * item.price, 0);
}

function renderCartItems() {
  const cartItems = document.getElementById("cart-items");
  const cart = getCart();
  cartItems.innerHTML = "";

  if (cart.length === 0) {
    cartItems.innerHTML =
      '<tr><td colspan="5" class="empty-cart">Votre panier est vide.</td></tr>';
    return;
  }

  cart.forEach((item) => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td class="item-name">${item.name}</td>
      <td class="item-price">${formatCurrency(item.price)}</td>
      <td><input type="number" min="1" value="${item.quantity}" class="qty-input" data-product-id="${item.id}"></td>
      <td class="item-total">${formatCurrency(item.quantity * item.price)}</td>
      <td><button class="btn-delete" data-product-id="${item.id}">Supprimer</button></td>
    `;
    cartItems.appendChild(row);
  });
}

function renderSummary() {
  const cart = getCart();
  const total = getCartTotal(cart);
  const count = cart.reduce((sum, item) => sum + item.quantity, 0);

  const summaryText = document.getElementById("panier-count");
  summaryText.textContent =
    count > 0
      ? `Vous avez ${count} article${count > 1 ? "s" : ""}. Total estimé.`
      : "Votre panier est vide.";

  const totalAmount = document.querySelector(".panier-total-amount");
  totalAmount.textContent = formatCurrency(total);

  const checkoutButton = document.getElementById("checkout-button");
  if (checkoutButton) {
    checkoutButton.disabled = cart.length === 0;
  }
}

function bindCartEvents() {
  document.querySelectorAll(".btn-delete").forEach((button) => {
    button.addEventListener("click", (event) => {
      const productId = event.currentTarget.dataset.productId;
      const updatedCart = getCart().filter(
        (item) => String(item.id) !== String(productId),
      );
      setCart(updatedCart);
      renderCartItems();
      renderSummary();
      updateCartCount();
      bindCartEvents();
      bindQuantityEvents();
      saveCartData();
    });
  });
}

function bindQuantityEvents() {
  document.querySelectorAll(".qty-input").forEach((input) => {
    input.addEventListener("change", (event) => {
      const productId = event.currentTarget.dataset.productId;
      const quantity = Math.max(1, Number(event.currentTarget.value));
      const cart = getCart().map((item) => {
        if (String(item.id) === String(productId)) {
          item.quantity = quantity;
        }
        return item;
      });
      setCart(cart);
      renderCartItems();
      renderSummary();
      updateCartCount();
      bindCartEvents();
      bindQuantityEvents();
      saveCartData();
    });
  });
}

function saveCartData() {
  const input = document.getElementById("cart-data");
  if (input) {
    input.value = JSON.stringify(getCart());
  }
}

function attachCheckoutForm() {
  const button = document.getElementById("checkout-button");
  if (!button) {
    return;
  }

  button.addEventListener("click", () => {
    saveCartData();
    document.getElementById("cart-submit-form").submit();
  });
}

function initPanierPage() {
  renderCartItems();
  renderSummary();
  bindCartEvents();
  bindQuantityEvents();
  saveCartData();
  attachCheckoutForm();
}

document.addEventListener("DOMContentLoaded", () => {
  if (document.getElementById("cart-items")) {
    initPanierPage();
  }
});
