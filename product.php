<?php
require_once __DIR__ . '/config/db.php';

$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$productId) {
    http_response_code(404);
    echo '<p>Produit introuvable.</p>';
    exit;
}

$mysqli = getDbConnection();
$stmt = $mysqli->prepare('SELECT id, name, description, price, category, image, stock FROM products WHERE id = ?');
$stmt->bind_param('i', $productId);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    http_response_code(404);
    echo '<p>Produit introuvable.</p>';
    exit;
}

$isOutOfStock = ((int) $product['stock'] <= 0);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopKamer - <?= htmlspecialchars($product['name']) ?></title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/product.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="section-title">
            <div>
                <h2>Produit <span>ShopKamer</span></h2>
                <p>Détail complet du produit sélectionné.</p>
            </div>
            <a href="catalogue.php" class="btn">Retour au catalogue</a>
        </section>

        <section class="product-detail">
            <div class="product-detail-image">
                <img src="<?= htmlspecialchars(str_replace('images/', 'assets/images/', $product['image'])) ?>"
                     alt="<?= htmlspecialchars($product['name']) ?>">
            </div>

            <div class="product-detail-info">
                <h1><?= htmlspecialchars($product['name']) ?></h1>
                <span class="product-category">Catégorie : <?= htmlspecialchars($product['category']) ?></span>
                <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>

                <div class="product-detail-meta">
                    <span>Prix : <?= number_format($product['price'], 0, ',', ' ') ?> FCFA</span>
                    <span class="stock">Stock disponible : <?= htmlspecialchars($product['stock']) ?></span>
                </div>

                <div class="product-detail-actions">
                    <button class="btn" <?= $isOutOfStock ? 'disabled' : '' ?>>Ajouter au panier</button>
                    <?php if ($isOutOfStock): ?>
                        <span class="stock-empty">Rupture de stock</span>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
