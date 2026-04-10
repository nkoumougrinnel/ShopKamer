<?php
// Charge le contenu JSON depuis le fichier local puis le convertit en tableau PHP.
$jsonData   = json_decode(file_get_contents('donnees_test.json'), true);

// Récupère la liste complète des produits stockée sous la clé "products".
$products   = $jsonData['products'];

// Extrait les catégories de tous les produits, supprime les doublons et trie le résultat.
$categories = array_unique(array_column($products, 'category'));
sort($categories);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopKamer - Catalogue</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/catalogue.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <!-- En-tête de page -->
        <section class="section-title">
            <div>
                <h2>Catalogue <span>ShopKamer</span></h2>
                <p>Découvrez notre sélection de produits de qualité. Parcourez les catégories et trouvez exactement ce qu'il vous faut.</p>
            </div>
            <a href="panier.php" class="btn">Voir le panier</a>
        </section>

        <!-- Filtres générés depuis les catégories du JSON -->
        <div class="filters">
            <button class="filter-btn active" data-filter="tous">Tous</button>
            <?php foreach ($categories as $cat): ?>
                <button class="filter-btn" data-filter="<?= htmlspecialchars(strtolower($cat)) ?>">
                    <?= htmlspecialchars(ucfirst($cat)) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Grille de produits générée depuis le JSON -->
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <article class="product-card" data-category="<?= htmlspecialchars(strtolower($product['category'])) ?>">
                    <div class="product-img">
                        <img
                            src="<?= htmlspecialchars(str_replace('images/', 'assets/images/', $product['image'])) ?>"
                            alt="<?= htmlspecialchars($product['name']) ?>"
                            loading="lazy">
                    </div>
                    <div class="product-info">
                        <div class="product-brand"><?= htmlspecialchars($product['category']) ?></div>
                        <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                        <p class="product-desc"><?= htmlspecialchars($product['description']) ?></p>
                        <div class="product-footer">
                            <div class="product-price">
                                <?= number_format($product['price'], 0, ',', ' ') ?> FCFA
                            </div>
                            <button class="add-btn" title="Ajouter au panier">+</button>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const filter = this.dataset.filter;
                document.querySelectorAll('.product-card').forEach(card => {
                    card.style.display =
                        (filter === 'tous' || card.dataset.category === filter) ? '' : 'none';
                });
            });
        });
    </script>
</body>
</html>