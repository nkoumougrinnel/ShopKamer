const CART_STORAGE_KEY = "shopkamer_cart";

function getCart() {
  const raw = localStorage.getItem(CART_STORAGE_KEY);
  return raw ? JSON.parse(raw) : [];
}

function saveCart(cart) {
  localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
}

function calculateCartCount(cart = null) {
  const currentCart = cart || getCart();
  return currentCart.reduce((total, item) => total + Number(item.quantity), 0);
}

function updateCartCount() {
  const count = calculateCartCount();
  document.querySelectorAll(".cart-count").forEach((el) => {
    el.textContent = count;
  });
}

function findCartItem(cart, productId) {
  return cart.find((item) => Number(item.id) === Number(productId));
}

function ajouterAuPanier(product) {
  const cart = getCart();
  const existing = findCartItem(cart, product.id);

  if (existing) {
    existing.quantity = Math.min(existing.quantity + 1, Number(product.stock));
  } else {
    cart.push({
      id: Number(product.id),
      name: product.name,
      price: Number(product.price),
      stock: Number(product.stock),
      image: product.image,
      quantity: 1,
    });
  }

  saveCart(cart);
  updateCartCount();
  return cart;
}

function supprimerDuPanier(productId) {
  const cart = getCart().filter(
    (item) => Number(item.id) !== Number(productId),
  );
  saveCart(cart);
  updateCartCount();
  return cart;
}

function modifierQuantite(productId, quantity) {
  const cart = getCart();
  const item = findCartItem(cart, productId);

  if (!item) {
    return cart;
  }

  const qty = Math.max(1, Number(quantity));
  item.quantity = Math.min(qty, Number(item.stock));

  saveCart(cart);
  updateCartCount();
  return cart;
}

function initAddToCartButtons() {
  document.querySelectorAll(".add-to-cart").forEach((button) => {
    button.addEventListener("click", (event) => {
      event.preventDefault();
      const target = event.currentTarget;
      const product = {
        id: target.dataset.productId,
        name: target.dataset.productName,
        price: target.dataset.productPrice,
        stock: target.dataset.productStock,
        image: target.dataset.productImage,
      };
      ajouterAuPanier(product);
      const message =
        target.dataset.successMessage || "Produit ajouté au panier.";
      showGlobalAlert(message);
    });
  });
}

function showGlobalAlert(message) {
  const alert = document.createElement("div");
  alert.className = "toast";
  alert.textContent = message;
  document.body.appendChild(alert);

  setTimeout(() => {
    alert.remove();
  }, 1800);
}

document.addEventListener("DOMContentLoaded", () => {
  updateCartCount();
  initAddToCartButtons();
});
