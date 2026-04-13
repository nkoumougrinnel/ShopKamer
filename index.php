<?php 
// Récupère les données JSON du fichier local et les transforme en tableau PHP.
$jsonData = json_decode(file_get_contents('donnees_test.json'), true);

// Récupère la liste complète des produits dans le tableau JSON.
$products = $jsonData['products'];

// Sélectionne les 4 premiers produits pour l'affichage en vedette sur la page d'accueil.
$featuredProducts = array_slice($products, 0, 4);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopKamer</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <!-- Hero -->
        <section class="hero">
            <h1>Bienvenue sur ShopKamer</h1>
            <p>Découvrez une sélection curatée de produits de qualité, des articles de mode aux technologies dernière génération.</p>
            <div class="hero-actions">
                <a href="catalogue.php" class="btn">Parcourir le Catalogue</a>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="login.php" class="btn-secondary">Se connecter</a>
                <?php endif; ?>
            </div>
        </section>

        <!-- Catégories -->
        <section class="home-section">
            <h2>Nos Catégories</h2>
            <div class="categories-grid">
                <a href="catalogue.php?cat=mode" class="category-card">
                    <h3>Mode</h3>
                    <p>Vêtements et accessoires</p>
                </a>
                <a href="catalogue.php?cat=tech" class="category-card">
                    <h3>Technologie</h3>
                    <p>Appareils et gadgets</p>
                </a>
                <a href="catalogue.php?cat=maison" class="category-card">
                    <h3>Maison</h3>
                    <p>Décoration et ameublement</p>
                </a>
                <a href="catalogue.php" class="category-card">
                    <h3>Divers</h3>
                    <p>Et bien d'autres articles</p>
                </a>
            </div>
        </section>

        <!-- Produits en vedette -->
        <section class="home-section">
            <h2>Produits en Vedette</h2>
            <div class="featured-grid">
                <?php foreach ($featuredProducts as $product): ?>
                <article class="featured-card">
                    <div class="featured-card-img">
                        <img
                            src="<?= htmlspecialchars(str_replace('images/', 'assets/images/', $product['image'])) ?>"
                            alt="<?= htmlspecialchars($product['name']) ?>"
                            loading="lazy">
                    </div>
                    <div class="featured-card-body">
                        <div class="featured-card-category"><?= htmlspecialchars($product['category']) ?></div>
                        <div class="featured-card-name"><?= htmlspecialchars($product['name']) ?></div>
                        <p class="featured-card-desc">
                            <?= htmlspecialchars(substr($product['description'], 0, 70)) ?>…
                        </p>
                        <div class="featured-card-footer">
                            <span class="featured-card-price">
                                <?= number_format($product['price'], 0, ',', ' ') ?> FCFA
                            </span>
                            <button class="featured-add-btn" title="Ajouter au panier">+</button>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            <div class="featured-section-cta">
                <a href="catalogue.php" class="btn">Voir Tous les Produits</a>
            </div>
        </section>

        <!-- Pourquoi ShopKamer -->
        <section class="home-section">
            <h2>Pourquoi ShopKamer ?</h2>
            <div class="why-grid">
                <div class="why-item">
                    <h3>✓ Qualité Garantie</h3>
                    <p>Tous nos produits sont soigneusement sélectionnés pour assurer la meilleure qualité.</p>
                </div>
                <div class="why-item">
                    <h3>✓ Prix Compétitifs</h3>
                    <p>Nous proposons les meilleures offres avec un rapport qualité-prix imbattable.</p>
                </div>
                <div class="why-item">
                    <h3>✓ Service Client</h3>
                    <p>Notre équipe est disponible pour répondre à vos questions et préoccupations.</p>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>