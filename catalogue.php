<?php
require_once __DIR__ . '/config/db.php';

$mysqli = getDbConnection();

$selectedCategory = trim((string) ($_GET['categorie'] ?? ''));
if (strtolower($selectedCategory) === 'tous') {
    $selectedCategory = '';
}
$searchTerm = trim((string) ($_GET['search'] ?? ''));

// Liste des catégories disponibles.
$categoryResult = $mysqli->query('SELECT DISTINCT category FROM products ORDER BY category ASC');
$categories = $categoryResult->fetch_all(MYSQLI_ASSOC);
$categoryResult->close();

// Construction de la requête produit.
$sql = 'SELECT id, name, description, price, category, image, stock FROM products WHERE 1 = 1';
$types = '';
$params = [];

if ($selectedCategory !== '') {
    $sql .= ' AND LOWER(category) = ?';
    $types .= 's';
    $params[] = strtolower($selectedCategory);
}

if ($searchTerm !== '') {
    $sql .= ' AND name LIKE ?';
    $types .= 's';
    $params[] = '%' . $searchTerm . '%';
}

$sql .= ' ORDER BY name ASC';

$stmt = $mysqli->prepare($sql);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
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

        <form method="get" class="search-form">
            <input
                type="search"
                name="search"
                placeholder="Rechercher un produit..."
                value="<?= htmlspecialchars($searchTerm) ?>"
            >
            <?php if ($selectedCategory !== ''): ?>
                <input type="hidden" name="categorie" value="<?= htmlspecialchars($selectedCategory) ?>">
            <?php endif; ?>
            <button type="submit" class="btn">Rechercher</button>
        </form>

        <div class="filters">
            <a href="catalogue.php<?= $searchTerm !== '' ? '?search=' . urlencode($searchTerm) : '' ?>" class="filter-btn <?= $selectedCategory === '' ? 'active' : '' ?>">
                Tous
            </a>
            <?php foreach ($categories as $cat): ?>
                <?php $catValue = $cat['category']; ?>
                <a href="catalogue.php?categorie=<?= urlencode($catValue) ?><?= $searchTerm !== '' ? '&search=' . urlencode($searchTerm) : '' ?>" class="filter-btn <?= strtolower($selectedCategory) === strtolower($catValue) ? 'active' : '' ?>">
                    <?= htmlspecialchars($catValue) ?>
                </a>
            <?php endforeach; ?>
        </div>

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
                            <div class="product-actions">
                                <a href="product.php?id=<?= urlencode($product['id']) ?>" class="btn">Voir</a>
                                <button
                                    class="add-btn add-to-cart"
                                    title="Ajouter au panier"
                                    data-product-id="<?= htmlspecialchars($product['id']) ?>"
                                    data-product-name="<?= htmlspecialchars($product['name']) ?>"
                                    data-product-price="<?= htmlspecialchars($product['price']) ?>"
                                    data-product-stock="<?= htmlspecialchars($product['stock'] ?? 0) ?>"
                                    data-product-image="<?= htmlspecialchars($product['image']) ?>"
                                    data-success-message="<?= htmlspecialchars($product['name']) ?> ajouté au panier"
                                >+
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>