<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopKamer - Panier</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/panier.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <!-- En-tête de page -->
        <section class="section-title">
            <div>
                <h2>Mon <span>Panier</span></h2>
                <p>Consultez vos articles sélectionnés avant le passage en caisse.</p>
            </div>
            <a href="catalogue.php" class="btn">Retour au catalogue</a>
        </section>

        <!-- Résumé du panier -->
        <div class="panier-summary">
            <div class="panier-summary-header">
                <div>
                    <h3>Résumé du Panier</h3>
                    <p id="panier-count">Votre panier est vide.</p>
                </div>
                <a href="catalogue.php" class="btn-secondary">Continuer mes achats</a>
            </div>

            <table class="panier-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Prix</th>
                        <th>Quantité</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="cart-items">
                    <tr>
                        <td colspan="5" class="empty-cart">Chargement du panier…</td>
                    </tr>
                </tbody>
            </table>
            <form id="cart-submit-form" action="paiement.php" method="post" style="display:none;">
                <input type="hidden" name="cart_data" id="cart-data" value="">
            </form>

            <div class="panier-footer">
                <span class="panier-total-label">
                    Total : <strong class="panier-total-amount">0 FCFA</strong>
                </span>
                <button class="btn" type="button" id="checkout-button">Passer à la caisse</button>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/panier.js"></script>
</body>
</html>