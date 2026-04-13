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
                    <p>Vous avez 3 articles. Total estimé.</p>
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
                <tbody>
                    <tr>
                        <td class="item-name">Sac à dos urbain</td>
                        <td class="item-price">33 000 FCFA</td>
                        <td><input type="number" value="1" min="1" class="qty-input"></td>
                        <td class="item-total">33 000 FCFA</td>
                        <td><button class="btn-delete">Supprimer</button></td>
                    </tr>
                    <tr>
                        <td class="item-name">Enceinte Bluetooth portable</td>
                        <td class="item-price">55 000 FCFA</td>
                        <td><input type="number" value="1" min="1" class="qty-input"></td>
                        <td class="item-total">55 000 FCFA</td>
                        <td><button class="btn-delete">Supprimer</button></td>
                    </tr>
                    <tr>
                        <td class="item-name">Montre connectée fitness</td>
                        <td class="item-price">85 500 FCFA</td>
                        <td><input type="number" value="1" min="1" class="qty-input"></td>
                        <td class="item-total">85 500 FCFA</td>
                        <td><button class="btn-delete">Supprimer</button></td>
                    </tr>
                </tbody>
            </table>

            <div class="panier-footer">
                <span class="panier-total-label">
                    Total : <strong class="panier-total-amount">173 500 FCFA</strong>
                </span>
                <button class="btn">Passer à la caisse</button>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>